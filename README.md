
# Jabalega Admin

React frontend untuk dashboard admin Jabalega dengan Supabase sebagai authentication dan database.

## Local setup

1. Salin `.env.local.example` menjadi `.env.local`.
2. Isi URL project dan publishable key dari Supabase.
3. Jalankan `npm install` lalu `npm run dev`.

## Environment variables

`VITE_SUPABASE_ANON_KEY` boleh memakai publishable/anon key. Jangan masukkan secret key atau password database ke frontend.

## Production build

```bash
npm run build
```

Deploy ke Vercel dengan build command `npm run build` dan output directory `public`.