drop table if exists war_allowed_users;
create table war_allowed_users(
	war_id int not null,
	user_id int not null,
	primary key(war_id, user_id),
	foreign key(war_id) references war(id) on delete cascade,
	foreign key(user_id) references user(id) on delete cascade
);

drop procedure if exists p_war_allow_user;
delimiter //
create procedure p_war_allow_user(varWarId int, varUserId int)
begin
	insert into war_allowed_users(war_id, user_id) values(varWarId, varUserId);
end //
delimiter ;

drop procedure if exists p_war_disallow_user;
delimiter //
create procedure p_war_disallow_user(varWarId int, varUserId int)
begin
	delete from war_allowed_users where war_id = varWarId and user_id = varUserId;
end //
delimiter ;

drop procedure if exists p_war_disallow_all_users;
delimiter //
create procedure p_war_disallow_all_users(varWarId int)
begin
	delete from war_allowed_users where war_id = varWarId;
end //
delimiter ;

drop procedure if exists p_war_get_allowed_users;
delimiter //
create procedure p_war_get_allowed_users(varWarId int)
begin
	select user_id from war_allowed_users where war_id = varWarId;
end //
delimiter ;

drop table if exists war_edit_requests;
create table war_edit_requests(
	war_id int not null,
	user_id int not null,
	message varchar(256),
	primary key(war_id, user_id),
	foreign key(war_id) references war(id) on delete cascade,
	foreign key(user_id) references user(id) on delete cascade
);

drop procedure if exists p_war_edit_request_create;
delimiter //
create procedure p_war_edit_request_create(varWarId int, varUserId int, varMessage varchar(256))
begin
	insert into war_edit_requests values(varWarId, varUserId, varMessage);
end //
delimiter ;

drop procedure if exists p_war_edit_request_delete;
delimiter //
create procedure p_war_edit_request_delete(varWarId int, varUserId int)
begin
	delete from war_edit_requests where war_id = varWarId and user_id = varUserId;
end //
delimiter ;

drop procedure if exists p_war_get_edit_requests;
delimiter //
create procedure p_war_get_edit_requests(varWarId int)
begin
	select user.*, war_edit_requests.message from user join war_edit_requests on id = user_id where war_id = varWarId;
end //
delimiter ;