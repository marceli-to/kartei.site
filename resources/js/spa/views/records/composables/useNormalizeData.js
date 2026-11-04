import { ref } from 'vue';

// State is per call: a page gets its own lists, nothing carries over from the previous archive
export function useNormalizeData() {
  const categoriesRegisters = ref([]);
  const categories = ref([]);
  const registers = ref([]);
  const tags = ref([]);

  const normalizeCategoryRegisterData = (data) => {
    categories.value = data.categories.map(cat => ({
      label: cat.name,
      value: cat.uuid
    }));

    registers.value = data.registers.map(reg => ({
      label: reg.name,
      value: reg.uuid
    }));
  };

  const normalizeTagsData = (data) => {
    tags.value = data.map(tag => ({
      label: tag.name,
      value: tag.uuid
    }));
  };

  const normalizeCategoryData = (data) => {
    const result = [];

    data.forEach(cat => {
      result.push({
        prefix: `${cat.number}.`,
        label:  cat.name,
        value: cat.uuid,
        labelClass: 'font-muoto-medium'
      });

      cat.registers?.forEach(reg => {
        result.push({
          prefix: `${reg.number}.`,
          label: reg.name,
          value: reg.uuid,
        });
      });
    });

    categoriesRegisters.value = result;

  };

  return {
    categoriesRegisters,
    categories,
    registers,
    tags,
    normalizeCategoryData,
    normalizeCategoryRegisterData,
    normalizeTagsData
  };
}
