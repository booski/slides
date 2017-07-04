alter table `slide` add column `type` varchar(64);
update `slide` set `type` = 'image' where `type` is NULL;
