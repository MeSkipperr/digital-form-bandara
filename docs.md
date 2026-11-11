# Auto Generate `riwayat_pengecekan`

Every day at **00:00 WITA**, the app creates one pending inspection record (`riwayat_pengecekan`) for each `form_alat` row. Officers then fill these records in during the day.

Each generated row looks like this:

| Column         | Value                    |
| -------------- | ------------------------ |
| `id`           | auto increment           |
| `id_form_alat` | id of the `form_alat`    |
| `id_pengguna`  | `NULL`                   |
| `catatan`      | `NULL`                   |
| `status`       | `tertunda`               |
| `dicek_pada`   | `NULL`                   |

---

## Files

| File | Purpose |
| ---- | ------- |
| `app/Console/Commands/GenerateRiwayatPengecekanHarian.php` | The Artisan command that inserts the rows and writes the log. |
| `routes/console.php` | Registers the command with the Laravel scheduler (every 10 s locally, daily at 00:00 otherwise). |
| `config/logging.php` | Defines the `riwayat_pengecekan` log channel, which writes to `storage/logs/riwayat-pengecekan.log`. |
| `database/migrations/2026_11_11_000007_create_riwayat_pengecekan_table.php` | `id_pengguna` is nullable and `status` defaults to `'tertunda'`. |
| `app/Models/RiwayatPengecekan.php` | `$attributes` sets the model's default `status` to `'tertunda'`. |
| `database/factories/RiwayatPengecekanFactory.php` | The `tertunda()` state builds a row matching the generated ones (nulls plus `tertunda`). |
| `tests/Feature/GenerateRiwayatPengecekanHarianTest.php` | Feature tests for the command, the log and the schedule. |

---

## Functions

### `GenerateRiwayatPengecekanHarian`

- **Signature:** `riwayat-pengecekan:generate`
- **Description:** `Buat riwayat pengecekan berstatus tertunda untuk setiap form alat`
- **Constant `UKURAN_CHUNK = 500`:** the number of `form_alat` rows handled per bulk insert.

#### `handle(): int`

1. Opens the `riwayat_pengecekan` log channel and records the start time.
2. Logs `Generate riwayat_pengecekan dimulai` with the current `waktu`.
3. Reads `form_alat` ids in chunks of 500 with `FormAlat::query()->select('id')->chunkById(...)`.
4. For each chunk, runs **one** bulk `RiwayatPengecekan::query()->insert([...])` that adds a `tertunda` row for every id in that chunk.
5. Counts the inserted rows in `$jumlahDibuat`.
6. On success, logs `Generate riwayat_pengecekan selesai` with `jumlah` (rows created) and `durasi_ms`, prints the count to the console, and returns `self::SUCCESS`.
7. On any exception, logs `Generate riwayat_pengecekan gagal` with `pesan`, reports the exception, and returns `self::FAILURE`. The scheduler then marks the run as failed.

### Schedule (`routes/console.php`)

```php
$generateRiwayatPengecekan = Schedule::command('riwayat-pengecekan:generate')
    ->withoutOverlapping()
    ->onOneServer();

if (app()->isLocal()) {
    $generateRiwayatPengecekan->everyTenSeconds();
} else {
    $generateRiwayatPengecekan->dailyAt('00:00')->timezone('Asia/Makassar');
}
```

- `withoutOverlapping()`: a new run won't start while the previous one is still going.
- `onOneServer()`: with several servers, only one runs the job. This needs a cache store that supports locks.
- `APP_ENV=local`: runs **every 10 seconds**, for testing.
- Any other `APP_ENV` (for example `production`): runs **daily at 00:00 Asia/Makassar (WITA)**. `config/app.php` uses UTC, so the explicit timezone is required. Change it to `Asia/Jakarta` if WIB is needed.

---

## How It Generates

```
System cron (every minute)
        │
        ▼
php artisan schedule:run
        │  is it 00:00 WITA? (or every 10 s when local)
        ▼
php artisan riwayat-pengecekan:generate
        │
        ├─ log "dimulai"
        ├─ SELECT id FROM form_alat ... (chunks of 500)
        │     └─ INSERT INTO riwayat_pengecekan (one query per chunk)
        └─ log "selesai" (jumlah, durasi_ms)  or  "gagal" (pesan)
```

Example log output (`storage/logs/riwayat-pengecekan.log`):

```
[2026-11-12 00:00:00] production.INFO: Generate riwayat_pengecekan dimulai {"waktu":"2026-11-12 00:00:00"}
[2026-11-12 00:00:00] production.INFO: Generate riwayat_pengecekan selesai {"jumlah":42,"durasi_ms":18}
```

---

## Running It

Run once by hand:

```bash
php artisan riwayat-pengecekan:generate
```

Test the schedule locally (every 10 s, with `APP_ENV=local`):

```bash
php artisan schedule:work
```

```bash
tail -f storage/logs/riwayat-pengecekan.log
```

Clear the test rows afterwards. This wipes the local database:

```bash
php artisan migrate:fresh
```

Show the registered schedule:

```bash
php artisan schedule:list
```

Run the tests:

```bash
php artisan test --compact tests/Feature/GenerateRiwayatPengecekanHarianTest.php
```

## Server Setup

Set `APP_ENV=production` in `.env`, then add this single crontab entry (`crontab -e`):

```
* * * * * cd /path/to/digital-form-bandara && php artisan schedule:run >> /dev/null 2>&1
```

---

## Notes

- **Not idempotent:** the table has no date column, so running the command twice on the same day creates two sets of rows. The scheduler itself runs it only once a day. To make reruns safe, add a `tanggal` column with a unique `(id_form_alat, tanggal)` index.
- **Seeder:** `DatabaseSeeder` uses `WithoutModelEvents`. That doesn't affect this command, because the command doesn't rely on model events.
