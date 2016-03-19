drop procedure if exists p_get_api_keys;
delimiter //
create procedure p_get_api_keys()
begin
	select * from api_keys order by ip;
end //
delimiter ;