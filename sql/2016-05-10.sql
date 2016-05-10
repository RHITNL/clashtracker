alter table user add last_login datetime;

drop procedure if exists p_user_login;
delimiter //
create procedure p_user_login(varId int, varLoginTime datetime)
	begin
		update user set last_login = varLoginTime where id = varId;
	end //
delimiter ;