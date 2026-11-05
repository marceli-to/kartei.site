import { ref } from 'vue';
import { getArchiveMeta } from '@/services/api/archive';

/**
 * Categories, registers, tags and card settings of an archive, as options
 * for the record list filters and the record form.
 *
 * State is per call: a page gets its own lists, nothing carries over from
 * the previous archive.
 *
 *   const { categories, tags, settings, loadArchiveMeta } = useArchiveMeta();
 *   await loadArchiveMeta(archiveUuid);
 */
export function useArchiveMeta() {
  // Flat list for the record form: each category followed by its registers
  const categoriesRegisters = ref([]);
  const categories = ref([]);
  const registers = ref([]);
  const tags = ref([]);
  // { has_images, record_fields: [{ uuid, placeholder }] }
  const settings = ref({ has_images: null, record_fields: [] });

  const toOption = (item) => ({ label: item.name, value: item.uuid });

  const loadArchiveMeta = async (archiveUuid) => {
    const meta = await getArchiveMeta(archiveUuid);

    categories.value = meta.categories.map(toOption);
    // In structure order, each with the uuid of its category
    registers.value = meta.categories_registers.flatMap((cat) =>
      (cat.registers ?? []).map((reg) => ({ ...toOption(reg), category: cat.uuid })),
    );
    tags.value = meta.tags.map(toOption);

    categoriesRegisters.value = meta.categories_registers.flatMap((cat) => [
      { prefix: `${cat.number}.`, label: cat.name, value: cat.uuid, labelClass: 'font-muoto-medium' },
      ...(cat.registers ?? []).map((reg) => ({ prefix: `${reg.number}.`, label: reg.name, value: reg.uuid })),
    ]);

    settings.value = meta.settings ?? { has_images: null, record_fields: [] };
    return meta;
  };

  return {
    categoriesRegisters,
    categories,
    registers,
    tags,
    settings,
    loadArchiveMeta,
  };
}
