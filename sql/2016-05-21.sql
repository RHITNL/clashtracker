alter table player_stats add column deletable boolean default false;

drop procedure if exists p_player_record_loot;
delimiter //
create procedure p_player_record_loot(varId int, varType varchar(2), varAmount int, varDate varchar(50))
begin
  update player_stats set deletable = false where player_id = varId and stat_type = varType;
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount, deletable) values (varId, varDate, varType, varAmount, true);
end //
delimiter ;

drop procedure if exists p_player_delete_record;
delimiter //
create procedure p_player_delete_record(varId int, varType varchar(2))
BEGIN
  delete from player_stats where player_id = varId and stat_type = varType and deletable = true;
end //
delimiter ;