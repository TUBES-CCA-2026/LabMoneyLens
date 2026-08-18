# LabMoneyLens — Phase 2B: Transaction Integrity

Phase 2B replaces timestamp-based transaction grouping with a dedicated `transaction_group_id` and makes multi-item financial operations atomic.

## Main changes

- Added `transaction_group_id` to `pemasukan` and `pengeluaran`.
- Existing records are backfilled using the previous `created_at` grouping rule.
- New transactions receive a UUID group ID shared by all items in that transaction.
- Pemasukan/Pengeluaran create operations use `DB::transaction()` and batch inserts.
- Pengeluaran edit validates the quantity-adjusted final total before any item is soft-deleted.
- Pemasukan/Pengeluaran edit operations are atomic: failure rolls back all changes.
- Edit/delete/group lookup uses `transaction_group_id`, not `created_at`.
- Recycle Bin restore/delete operates on the whole transaction group.
- Receipt gallery groups by transaction group instead of file path.
- Replacing a receipt updates all items in the transaction group and cleans unused old files safely.
- Report grouping uses `transaction_group_id` instead of timestamp.
- Report queries now only include confirmed transactions (`is_confirmed = 1`).
- Category IDs in transaction requests are validated against their corresponding tables.
- Added feature tests for transaction grouping and quantity-aware expense updates.

## Migration

After copying the `.env` from your working project, run:

```bash
php artisan migrate
```

Do **not** run `migrate:fresh` or `migrate:refresh` against your existing database.

## Verification

Run:

```bash
php artisan migrate:status
php artisan test
```

For the existing local database, verify that the new migration is `Ran` and that existing income/expense rows have a non-null `transaction_group_id`.
