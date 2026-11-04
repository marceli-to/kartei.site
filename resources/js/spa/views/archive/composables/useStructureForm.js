import { ref, toRaw } from 'vue';
import { getCategories, storeCategory } from '@/services/api/category';
import { formatNumber } from '@/utils/numbering';

const emptyRegister = () => ({ name: '', custom_id: '', uuid: null });
const emptyCategory = () => ({ name: '', custom_id: '', uuid: null, registers: [] });

/**
 * The structure (categories with their registers) of one archive, as edited
 * in archive/Category.vue: loading, numbering, auto IDs and saving.
 *
 * Numeral types: 'decimal', 'alpha', 'roman'; ID type: 'auto' (first two
 * letters of the title) or 'manual'.
 */
export function useStructureForm(archiveUuid) {
  const categories = ref([emptyCategory()]);
  const numeralsCategory = ref('decimal');
  const numeralsRegister = ref('decimal');
  const idType = ref('auto');
  // Whether the archive has saved categories (opens the info box when not)
  const hasCategories = ref(false);

  // Stable drag keys per row object. custom_id can be empty or duplicated
  // ("Bilder" and "Bibliothek" both become "BI") and changes while typing.
  const rowKeys = new WeakMap();
  let nextRowKey = 1;
  const rowKey = (row) => {
    const raw = toRaw(row);
    if (!rowKeys.has(raw)) rowKeys.set(raw, nextRowKey++);
    return rowKeys.get(raw);
  };

  // Display numbers: "1." for a category, "1.1" for its first register
  const categoryNumber = (cIdx) => formatNumber(cIdx, numeralsCategory.value, true);
  const registerNumber = (cIdx, rIdx) => categoryNumber(cIdx) + formatNumber(rIdx, numeralsRegister.value);

  const load = async () => {
    const response = await getCategories(archiveUuid);
    const data = Array.isArray(response.data) ? response.data : [];

    categories.value = data.length
      ? data.map(category => ({
          name: category.name,
          custom_id: category.custom_id ?? '',
          uuid: category.uuid,
          registers: (category.registers || []).map(register => ({
            name: register.name,
            custom_id: register.custom_id ?? '',
            uuid: register.uuid
          }))
        }))
      : [emptyCategory()];
    hasCategories.value = data.length > 0;

    // Registers store their own numeral type; read it from the first register there is
    const firstRegister = data.flatMap(category => category.registers || [])[0];
    numeralsCategory.value = data[0]?.numeral_type ?? 'decimal';
    numeralsRegister.value = firstRegister?.numeral_type ?? numeralsCategory.value;
    idType.value = data[0]?.custom_id_type ?? 'auto';
  };

  // In auto mode the ID is the first two letters of the name (category or register)
  const applyAutoId = (item) => {
    if (idType.value !== 'auto') return;
    item.custom_id = item.name.slice(0, 2).toUpperCase();
  };

  const addCategory = () => categories.value.push(emptyCategory());

  const addRegisterToLastCategory = () => {
    categories.value[categories.value.length - 1]?.registers.push(emptyRegister());
  };

  // Rows without any input are dropped before saving
  const removeEmptyRows = () => {
    categories.value = categories.value.filter(category => {
      category.registers = category.registers.filter(register =>
        register.name || register.custom_id || register.uuid
      );
      return category.name || category.custom_id || category.uuid || category.registers.length > 0;
    });
  };

  const buildPayload = () => categories.value.map((category, cIdx) => {
    // Stored numbers: "A" for a category, "A.b" for a register
    const number = formatNumber(cIdx, numeralsCategory.value).toUpperCase();
    return {
      numeral_type: numeralsCategory.value,
      custom_id_type: idType.value,
      uuid: category.uuid,
      number,
      name: category.name,
      custom_id: category.custom_id,
      order: cIdx,
      registers: category.registers.map((register, rIdx) => ({
        numeral_type: numeralsRegister.value,
        custom_id_type: idType.value,
        uuid: register.uuid,
        number: `${number}.${formatNumber(rIdx, numeralsRegister.value)}`,
        name: register.name,
        custom_id: register.custom_id,
        order: rIdx
      }))
    };
  });

  const isComplete = (payload) => payload.every(category =>
    category.custom_id && category.number && category.name &&
    category.registers.every(register => register.custom_id && register.number && register.name)
  );

  // Takes the uuids and IDs the server assigned
  const applySaved = (data) => {
    data.forEach((saved, cIdx) => {
      const category = categories.value[cIdx];
      if (!category) return;
      category.uuid = saved.uuid;
      category.custom_id = saved.custom_id;
      saved.registers?.forEach((savedRegister, rIdx) => {
        const register = category.registers[rIdx];
        if (!register) return;
        register.uuid = savedRegister.uuid;
        register.custom_id = savedRegister.custom_id;
      });
    });

    hasCategories.value = data.length > 0;
    if (categories.value.length === 0) categories.value.push(emptyCategory());
  };

  // Returns false when a row is incomplete (nothing is sent then)
  const save = async () => {
    removeEmptyRows();

    // Enter submits without a blur, so auto IDs may not be derived yet
    categories.value.forEach(category => {
      applyAutoId(category);
      category.registers.forEach(applyAutoId);
    });

    const payload = buildPayload();
    if (!isComplete(payload)) return false;

    const response = await storeCategory(archiveUuid, payload);
    applySaved(response.data);
    return true;
  };

  return {
    categories,
    numeralsCategory,
    numeralsRegister,
    idType,
    hasCategories,
    rowKey,
    categoryNumber,
    registerNumber,
    load,
    save,
    applyAutoId,
    addCategory,
    addRegisterToLastCategory,
  };
}
