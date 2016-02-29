drop procedure if exists p_player_get_loot;
delimiter // 
create procedure p_player_get_loot(varId int)
begin
	select * from loot where player_id = varId order by date_recorded desc;
end //
delimiter ;

drop procedure if exists p_clan_players_for_loot_report;
delimiter //
create procedure p_clan_players_for_loot_report(varId int, varType varchar(2), varDate datetime)
begin
	select player.* from loot join player on player.id = player_id where date_recorded > varDate and loot_type = varType and player_id in (select player_id from clan_member where clan_id = varId and rank != 'EX' and rank != 'KI') group by player_id having count(*) > 1;
end //
delimiter ;