export const TABLES = {
  pt: { label: 'Pendirian PT', color: '#2f6f95' },
  nib: { label: 'NIB / Perizinan', color: '#24735f' },
  pirt: { label: 'PIRT', color: '#a56a19' },
  bpom: { label: 'BPOM', color: '#a6445f' },
  halal: { label: 'Sertifikasi Halal', color: '#4c873a' },
  merek: { label: 'HAKI / Merek', color: '#6354a8' },
  psat: { label: 'Izin PSAT', color: '#2876b8' },
  izin_lainnya: { label: 'Izin Lainnya', color: '#d26c3f' }
};

export const isSpecialTable = (table) => ['psat', 'izin_lainnya'].includes(table);
export const formatDate = (value) => value ? new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
