1.2.0
=====

UserBundle
----------

The User Bundle now required friendsofsymfony/user-bundle >= 2.0.0-beta1.

Required schema changes:

```sql
ALTER TABLE user DROP locked, DROP expired, DROP expires_at, DROP credentials_expired, DROP credentials_expire_at, CHANGE username username VARCHAR(180) NOT NULL, CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL;
CREATE UNIQUE INDEX UNIQ_8D93D649C05FB297 ON user (confirmation_token);
```
