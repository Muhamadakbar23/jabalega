import { supabase, configurationError } from './supabase';

const result = (data, error, fallback = {}) => error
  ? { success: false, message: error.message || 'Supabase request gagal.' }
  : { success: true, ...fallback, data };

const client = () => supabase || { ...result(null, { message: configurationError() }) };

export async function signIn(email, password) {
  if (!supabase) return client();
  const response = await supabase.auth.signInWithPassword({ email, password });
  return result(response.data?.user, response.error, { user: response.data?.user });
}

export async function signOut() {
  if (!supabase) return client();
  const response = await supabase.auth.signOut();
  return result(null, response.error);
}

export async function currentUser() {
  if (!supabase) return client();
  const response = await supabase.auth.getUser();
  return result(response.data?.user, response.error, { user: response.data?.user });
}

export async function subscribeToAuth(callback) {
  if (!supabase) return { data: { subscription: { unsubscribe() {} } } };
  return supabase.auth.onAuthStateChange((_event, session) => callback(session?.user || null));
}

export async function getTableData(table, page = 1, search = '') {
  if (!supabase) return client();
  const from = (page - 1) * 15;
  const to = from + 14;
  let query = supabase.from(table).select('*', { count: 'exact' }).order('nama').range(from, to);
  if (search) query = ['psat', 'izin_lainnya'].includes(table)
    ? query.or(`nama.ilike.%${search}%,nama_usaha.ilike.%${search}%,email.ilike.%${search}%,izin.ilike.%${search}%`)
    : query.or(`nama.ilike.%${search}%,nama_usaha.ilike.%${search}%,no_telefon.ilike.%${search}%`);
  const response = await query;
  return result(response.data || [], response.error, {
    total: response.count || 0,
    page,
    per_page: 15,
    total_pages: Math.max(1, Math.ceil((response.count || 0) / 15))
  });
}

export async function getRow(table, id) {
  if (!supabase) return client();
  const response = await supabase.from(table).select('*').eq('id', id).single();
  return result(response.data, response.error, { row: response.data });
}

export async function saveRow(table, form, id) {
  if (!supabase) return client();
  const payload = { ...form };
  delete payload.id; delete payload.created_at;
  const response = id
    ? await supabase.from(table).update(payload).eq('id', id).select().single()
    : await supabase.from(table).insert(payload).select().single();
  return result(response.data, response.error, { message: id ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.' });
}

export async function deleteRow(table, id) {
  if (!supabase) return client();
  const response = await supabase.from(table).delete().eq('id', id);
  return result(null, response.error, { message: 'Data berhasil dihapus.' });
}

export async function getStats(tables) {
  if (!supabase) return client();
  const counts = {};
  let total = 0;
  for (const table of tables) {
    const response = await supabase.from(table).select('*', { count: 'exact', head: true });
    if (response.error) return result(null, response.error);
    counts[table] = response.count || 0;
    total += counts[table];
  }
  return { success: true, total, table_counts: counts };
}