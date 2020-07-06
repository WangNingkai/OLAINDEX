CREATE TABLE IF NOT EXISTS "migrations" ("id" integer not null primary key autoincrement, "migration" varchar not null, "batch" integer not null);
CREATE TABLE IF NOT EXISTS "settings" ("id" integer not null primary key autoincrement, "name" varchar not null, "value" varchar null);
CREATE TABLE IF NOT EXISTS "users" ("id" integer not null primary key autoincrement, "name" varchar not null, "email" varchar not null, "password" varchar not null, "status" tinyint(1) not null, "created_at" datetime null, "updated_at" datetime null);
CREATE UNIQUE INDEX "users_email_unique" on "users" ("email");
INSERT INTO migrations VALUES(1,'2014_10_12_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'2019_10_09_143144_create_settings_table',1);
INSERT INTO users VALUES(1,'admin','admin@qq.com','$2y$10$qWhkwmfsMqdx3.U5jvMYKeHPLUSmp3Yxv1PFQ9wg.Er1BLHyg0.o6',1,NULL,NULL);

