# CHC Shipping JM Setup

1. Create a MySQL database named `chcshippingjm`.
2. Import `sql/schema.sql`.
3. Update DB/SMTP constants in `config/config.php`.
4. Serve `public/` as web root.
5. Seed admin user:

```sql
INSERT INTO users (name,email,password_hash,role)
VALUES ('Admin','admin@chcshipping.com', '$2y$10$k6Dq/VrYhox4M1lUjWqPruR6TUFzcvjS5kM6M8I4fYJ5MKfY.Zv3m', 'admin');
```

Default password hash above corresponds to `Password123!` and uses bcrypt-compatible `password_hash` output.
