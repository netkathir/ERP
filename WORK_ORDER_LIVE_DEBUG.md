# Work Order – Fix 500 Error on Live (Whyceeys)

Use this to see the real error on live and fix it. **Turn off debug again after you get the error message.**

---

## 1. See the actual error on live

### Option A – Turn on Laravel debug (temporary)

On the **live server** (via Whyceeys file manager / SSH / FTP):

1. Open the **`.env`** file in the project root.
2. Set:
   ```env
   APP_DEBUG=true
   LOG_LEVEL=debug
   ```
3. Save and reload the Work Order page.
4. You should see the **full error and stack trace** instead of "500 Server Error".
5. **Copy the full error text** (or take a screenshot).
6. **Immediately set again:**
   ```env
   APP_DEBUG=false
   LOG_LEVEL=error
   ```
   and save (so the site does not expose errors to visitors).

### Option B – Use Laravel log (no need to show errors in browser)

On the live server, open:

```text
storage/logs/laravel.log
```

Reproduce the 500 by opening the Work Order page, then check the **last lines** of `laravel.log`. The exception message and stack trace will be there.

---

## 2. Database – tables and migrations

Work Order needs these **tables** on the **live** database:

- `work_orders` (from migration `2025_12_10_000001_create_work_orders_tables.php`)
- `work_order_raw_materials` (same migration)
- `work_orders` must have column `quantity_blocks` (from `2025_12_10_000002_add_quantity_blocks_to_work_orders.php`)

Run on **live** (SSH or Whyceeys terminal / DB tool):

```bash
php artisan migrate
```

Or run the two migrations manually in your live DB tool:

- Run the SQL that creates `work_orders` and `work_order_raw_materials` (and the `quantity_blocks` column if your first migration doesn’t include it).

Other tables that Work Order depends on (must already exist):

- `branches`
- `customer_orders`
- `proforma_invoices`
- `users`
- `raw_materials`
- `units`

If any of these are missing, the 500 can be a foreign key or "table not found" error.

---

## 3. Clear cache on live

After code/DB changes, run on live:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

If you use route/config caching:

```bash
php artisan config:cache
php artisan route:cache
```

---

## 4. Permissions (Whyceeys / shared hosting)

Ensure the web server can write to:

- `storage/` (and everything inside, e.g. `storage/logs/`)
- `bootstrap/cache/`

Typical (if you have SSH):

```bash
chmod -R 775 storage bootstrap/cache
# If your server uses a web user (e.g. www-data):
# chown -R www-data:www-data storage bootstrap/cache
```

On Whyceeys, use their file manager to set permissions (e.g. 755 for dirs, 644 for files; 775 for `storage` and `bootstrap/cache` if allowed).

---

## 5. Quick checklist

| Check | Action |
|-------|--------|
| See error | Set `APP_DEBUG=true` temporarily or read `storage/logs/laravel.log` |
| Migrations | Run `php artisan migrate` on live (or apply the two work order migrations + `quantity_blocks` column) |
| Tables | Confirm `work_orders`, `work_order_raw_materials`, and `quantity_blocks` on `work_orders` exist |
| Cache | Run `config:clear`, `route:clear`, `view:clear`, `cache:clear` on live |
| Permissions | `storage/` and `bootstrap/cache/` writable by the web server |
| .env | Correct `DB_*`, `APP_URL`, and (after debugging) `APP_DEBUG=false` |

---

## 6. After you have the error message

Once you have the **exact error** from the screen or `laravel.log`, you can:

- Fix a missing table/column by running the right migration or SQL.
- Fix a missing class/file by ensuring all Work Order files are uploaded.
- Fix a permission error by adjusting `storage/` and `bootstrap/cache/`.

If you paste the **exact error text** (or the last 30–40 lines of `laravel.log` around the error), someone can tell you the precise fix for your Whyceeys live site.
