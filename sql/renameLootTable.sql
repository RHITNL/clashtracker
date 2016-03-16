rename table loot to player_stats;
alter table player_stats change loot_type stat_type varchar(2);
alter table player_stats change loot_amount stat_amount int;

drop procedure if exists p_player_record_loot;
delimiter // 
create procedure p_player_record_loot(varId int, varType varchar(2), varAmount int, varDate varchar(50))
begin
if(varDate = '%')
	then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, NOW(), varType, varAmount);
	else insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, varType, varAmount);
end if;
end //
delimiter ;

drop procedure if exists p_player_remove_loot;
delimiter //
create procedure p_player_remove_loot(varId int, varType varChar(2), varDate varChar(50))
begin
if(varDate ='%')
	then delete from player_stats where player_id = varId and stat_type = varType;
	else delete from player_stats where player_id = varId and stat_type = varType and date_recorded >= varDate;
end if;
end //
delimiter ;

drop procedure if exists p_player_get_loot;
delimiter // 
create procedure p_player_get_loot(varId int)
begin
	select * from player_stats where player_id = varId order by date_recorded desc;
end //
delimiter ;

drop procedure if exists p_clan_players_for_loot_report;
delimiter //
create procedure p_clan_players_for_loot_report(varId int, varType varchar(2), varDate datetime)
begin
	select player.* from player_stats join player on player.id = player_id where date_recorded > varDate and stat_type = varType and player_id in (select player_id from clan_member where clan_id = varId and rank != 'EX' and rank != 'KI') group by player_id having count(*) > 1;
end //
delimiter ;