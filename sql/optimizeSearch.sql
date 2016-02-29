drop procedure if exists p_clan_search;
delimiter //
create procedure p_clan_search(varQuery varChar(50))
begin
	select * from clan where lower(name) like lower(varQuery) or lower(tag) like lower(varQuery) limit 50;
end //
delimiter ;

drop procedure if exists p_player_search;
delimiter //
create procedure p_player_search(varQuery varChar(50))
begin
	select * from player where lower(name) like lower(varQuery) or lower(tag) like lower(varQuery) limit 50;
end //
delimiter ;