import { ref } from 'vue';

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
  tags.value = data.data.map(tag => ({
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

export function useNormalizeData() {
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
