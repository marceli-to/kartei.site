import { ref } from 'vue';

const structure = ref([]);
const categories = ref([]);
const registers = ref([]);
const tags = ref([]);

const normalizeCategoryData = (structureData) => {
  categories.value = structureData.categories.map(cat => ({
    label: cat.name,
    value: cat.uuid
  }));

  registers.value = structureData.registers.map(reg => ({
    label: reg.name,
    value: reg.uuid
  }));
};

const normalizeTagsData = (tagsData) => {
  tags.value = tagsData.data.map(tag => ({
    label: tag.name,
    value: tag.uuid
  }));
};

const normalizeStructureData = (structureData) => {
  const result = [];

  structureData.forEach(cat => {
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

  structure.value = result;

};

export function useNormalizeData() {
  return {
    structure,
    categories,
    registers,
    tags,
    normalizeStructureData,
    normalizeCategoryData,
    normalizeTagsData
  };
}
