alter table `slide` add column `type` varchar(64);
update `slide` set `type` = 'image' where `type` is NULL;
alter table `slide` drop primary key;
alter table `slide` add column `id` bigint(20) auto_increment primary key;
