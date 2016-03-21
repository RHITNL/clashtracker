alter table clan_member change rank rank_temp varchar(2);
alter table clan_member add column rank int;
update clan_member set rank = 1 where rank_temp = 'LE';
update clan_member set rank = 2 where rank_temp = 'CO';
update clan_member set rank = 3 where rank_temp = 'EL';
update clan_member set rank = 4 where rank_temp = 'ME';
update clan_member set rank = 5 where rank_temp = 'EX';
update clan_member set rank = 5 where rank_temp = 'KI';
alter table clan_member drop column rank_temp;

alter table clan change min_rank_access min_rank_access_temp varchar(2);
alter table clan add column min_rank_access int;
update clan set min_rank_access = 1 where min_rank_access_temp = 'LE';
update clan set min_rank_access = 2 where min_rank_access_temp = 'CO';
update clan set min_rank_access = 3 where min_rank_access_temp = 'EL';
update clan set min_rank_access = 4 where min_rank_access_temp = 'ME';
update clan set min_rank_access = 5 where min_rank_access_temp = 'EX';
update clan set min_rank_access = 5 where min_rank_access_temp = 'KI';
alter table clan drop column min_rank_access_temp;

alter table player change min_rank_access min_rank_access_temp varchar(2);
alter table player add column min_rank_access int;
update player set min_rank_access = 1 where min_rank_access_temp = 'LE';
update player set min_rank_access = 2 where min_rank_access_temp = 'CO';
update player set min_rank_access = 3 where min_rank_access_temp = 'EL';
update player set min_rank_access = 4 where min_rank_access_temp = 'ME';
update player set min_rank_access = 5 where min_rank_access_temp = 'EX';
update player set min_rank_access = 5 where min_rank_access_temp = 'KI';
alter table player drop column min_rank_access_temp;

drop procedure if exists p_clan_get_leader;
delimiter //
create procedure p_clan_get_leader(varClanId int)
begin
	select player_id from clan_member where clan_id = varClanId and rank = 1;
end //
delimiter ;

drop procedure if exists p_clan_add_player;
delimiter //
create procedure p_clan_add_player(varClanId int, varPlayerId int, varRank varchar(2))
begin
if exists (select * from clan_member where clan_id = varClanId and player_id = varPlayerId)
	then 
	if (varRank = 5)
	    then update clan_member set rank = varRank, date_modified = NOW(), date_left = NOW() where clan_id = varClanId and player_id = varPlayerId;
	    else update clan_member set rank = varRank, date_modified = NOW() where clan_id = varClanId and player_id = varPlayerId;
	end if;
	else insert into clan_member(clan_id, player_id, rank, date_created) values(varClanId, varPlayerId, varRank, NOW());
end if;
end //
delimiter ;

drop procedure if exists p_player_get_clan;
delimiter //
create procedure p_player_get_clan(varPlayerId int)
begin
	select clan_id from clan_member where player_id = varPlayerId and rank != 5;
end //
delimiter ;

drop procedure if exists p_player_leave_clan;
delimiter //
create procedure p_player_leave_clan(varPlayerId int)
begin
	update clan_member set rank = 'EX', date_modified = NOW(), date_left = NOW() where player_id = varPlayerId and rank != 5;
end //
delimiter ;

drop procedure if exists p_player_get_clan;
delimiter //
create procedure p_player_get_clan(varPlayerId int)
begin
	select clan.*, rank from clan_member join clan on clan.id = clan_id where player_id = varPlayerId and rank != 5;
end //
delimiter ;

drop procedure if exists p_clan_get_current_members;
delimiter //
create procedure p_clan_get_current_members(varClanId int, varOrder varchar(50))
begin
	set @st := concat('select player.*, rank from clan_member join player on player_id = player.id where clan_id = ', varClanId, ' and rank != 5 order by ', varOrder);
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200))
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 5;
end //
delimiter ;

drop procedure if exists p_clan_players_for_loot_report;
delimiter //
create procedure p_clan_players_for_loot_report(varId int, varType varchar(2), varDate datetime)
begin
	select player.* from loot join player on player.id = player_id where date_recorded > varDate and loot_type = varType and player_id in (select player_id from clan_member where clan_id = varId and rank != 5) group by player_id having count(*) > 1;
end //
delimiter ;

drop procedure if exists p_clan_players_for_loot_report;
delimiter //
create procedure p_clan_players_for_loot_report(varId int, varType varchar(2), varDate datetime)
begin
	select player.* from player_stats join player on player.id = player_id where date_recorded > varDate and stat_type = varType and player_id in (select player_id from clan_member where clan_id = varId and rank != 5) group by player_id having count(*) > 1;
end //
delimiter ;

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200), varDate datetime)
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 5;
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'LV', varLevel);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'TR', varTrophies);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'DO', varDonations);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'RE', varReceived);
end //
delimiter ;