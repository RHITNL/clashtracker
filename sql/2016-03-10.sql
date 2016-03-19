drop table if exists clan_edit_requests;
create table clan_edit_requests(
	clan_id int not null,
	user_id int not null,
	message varchar(256),
	primary key(clan_id, user_id),
	foreign key(clan_id) references clan(id) on delete cascade,
	foreign key(user_id) references user(id) on delete cascade
);

drop procedure if exists p_clan_edit_request_create;
delimiter //
create procedure p_clan_edit_request_create(varClanId int, varUserId int, varMessage varchar(256))
begin
	insert into clan_edit_requests values(varClanId, varUserId, varMessage);
end //
delimiter ;

drop procedure if exists p_clan_edit_request_delete;
delimiter //
create procedure p_clan_edit_request_delete(varClanId int, varUserId int)
begin
	delete from clan_edit_requests where clan_id = varClanId and user_id = varUserId;
end //
delimiter ;

drop procedure if exists p_clan_get_edit_requests;
delimiter //
create procedure p_clan_get_edit_requests(varClanId int)
begin
	select user.*, clan_edit_requests.message from user join clan_edit_requests on id = user_id where clan_edit_requests.clan_id = varClanId;
end //
delimiter ;
