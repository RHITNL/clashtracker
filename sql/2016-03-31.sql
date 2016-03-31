drop procedure if exists p_get_clans;
delimiter //
create procedure p_get_clans(varOrder varchar(50), varPageSize int)
begin
	set @st := concat('select * from clan order by ', varOrder, ' limit ', varPageSize, ';');
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_get_players;
delimiter //
create procedure p_get_players(varOrder varchar(50), varPageSize int)
begin
	set @st := concat('select * from player order by ', varOrder, ' limit ', varPageSize, ';');
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;