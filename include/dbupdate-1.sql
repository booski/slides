alter table `slide` add column `type` varchar(64);
update `slide` set `type` = 'image' where `type` is NULL;
alter table `slide` drop primary key;
alter table `slide` add column `id` bigint(20) auto_increment primary key;
update `show_image` set `image` = (select `id` from `slide` where `image` = `show_image`.`image`);
alter table `show_image` change column `image` `slide` bigint(20) not null;
rename table `show_image` to `show_slide`;
