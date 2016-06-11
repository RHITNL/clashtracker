drop procedure if exists p_war_delete;
delimiter //
create procedure p_war_delete(varId int)
begin
	delete from clan where id = varId;
	delete from war_player where war_id = varId;
	delete from war_attack where war_id = varId;
end //
delimiter ;