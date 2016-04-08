drop table if exists blog_post;
create table blog_post(
	name varchar(100) not null,
	content varchar(50000) not null,
	date_created datetime not null
);

drop procedure if exists p_blog_post_add;
delimiter //
create procedure p_blog_post_add(varName varchar(100), varContent varchar(50000), varDate datetime)
begin
	insert into blog_post values(varName, varContent, varDate);
end //
delimiter ;

drop procedure if exists p_get_blog_posts;
delimiter //
create procedure p_get_blog_posts(varBeforeDate datetime, varAfterDate datetime)
begin
	select * from blog_post where date_created < varBeforeDate and date_created > varAfterDate order by date_created desc;
end //
delimiter ;