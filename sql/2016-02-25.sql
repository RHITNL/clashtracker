drop procedure if exists p_get_players;
delimiter //
create procedure p_get_players(varPageSize int)
begin
	select * from player order by trophies desc limit varPageSize;
end //
delimiter ;

drop procedure if exists p_player_get_clan;
delimiter //
create procedure p_player_get_clan(varPlayerId int)
begin
	select clan.*, rank from clan_member join clan on clan.id = clan_id where player_id = varPlayerId and rank != 'KI' and rank != 'EX';
end //
delimiter ;

drop procedure if exists p_get_clans;
delimiter //
create procedure p_get_clans(varPageSize int)
begin
	select * from clan order by clan_points desc limit varPageSize;
end //
delimiter ;

drop procedure if exists p_clan_get_past_and_current_members;
delimiter //
create procedure p_clan_get_past_and_current_members(varClanId int)
begin
	select player.* from clan_member join player on player_id = player.id where clan_id = varClanId order by trophies desc;
end //
delimiter ;

drop procedure if exists p_clan_get_current_members;
delimiter //
create procedure p_clan_get_current_members(varClanId int)
begin
	select player.*, rank from clan_member join player on player_id = player.id where clan_id = varClanId and rank != 'KI' and rank != 'EX' order by trophies desc;
end //
delimiter ;

drop procedure if exists p_clan_get_wars;
delimiter //
create procedure p_clan_get_wars(varClanId int)
begin
	select * from war where first_clan_id = varClanId or second_clan_id = varClanId order by date_created desc;
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50))
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation where id = varId;
end //
delimiter ;

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200))
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 'KI' and rank != 'EX';
end //
delimiter ;

drop procedure if exists p_player_get_clans;
delimiter //
create procedure p_player_get_clans(varPlayerId int)
begin
	select * from clan where id in (select clan_id from clan_member where player_id = varPlayerId order by date_modified);
end //
delimiter ;

drop procedure if exists p_war_get_players;
delimiter //
create procedure p_war_get_players(varWarId int, varClanId int)
begin
if (varClanId = '%')
	then select player.* from player join war_player on war_player.player_id = player.id where war_player.war_id = varWarId order by war_player.rank;
	else select player.* from player join war_player on war_player.player_id = player.id where war_player.war_id = varWarId and clan_id = varClanId order by war_player.rank;
end if;
end //
delimiter ;

drop procedure if exists p_get_wars;
delimiter //
create procedure p_get_wars(varPageSize int)
begin
	select * from war order by date_created desc limit varPageSize;
end //
delimiter ;

alter table war add first_clan_stars int default 0;
alter table war add second_clan_stars int default 0;

drop procedure if exists p_player_get_wars;
delimiter //
create procedure p_player_get_wars(varPlayerId int)
begin
	select war.* from war_player join war on war.id = war_player.war_id where player_id = varPlayerId order by date_created desc;
end //
delimiter ;