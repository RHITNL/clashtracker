alter table clan drop column api_info;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime, varHourAgo datetime)
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified where id = varId;
	if (varClanPoints <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'CP' order by date_recorded desc limit 1))
		then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CP', varClanPoints);
	end if;
	if (varClanLevel <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'CL' order by date_recorded desc limit 1))
		then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CL', varClanLevel);
	end if;
	if (varMembers <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'ME' order by date_recorded desc limit 1))
		then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'ME', varMembers);
	end if;
	if (varWarWins <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'WW' order by date_recorded desc limit 1))
		then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'WW', varWarWins);
	end if;
end //
delimiter ;

alter table war add column stars_locked boolean;
alter table war add column first_clan_destruction float;
alter table war add column second_clan_destruction float;
alter table war add column first_clan_experience int;
alter table war add column second_clan_experience int;

drop procedure if exists p_war_update_bulk;
delimiter //
create procedure p_war_update_bulk(varId int, varClan1Stars int, varClan2Stars int, varClan1Destruction float, varClan2Destruction float, varClan1Exp int, varClan2Exp int)
BEGIN
  update war set stars_locked = true, first_clan_stars = varClan1Stars, second_clan_stars = varClan2Stars, first_clan_destruction = varClan1Destruction, second_clan_destruction = varClan2Destruction, first_clan_experience = varClan1Exp, second_clan_experience = varClan2Exp where id = varId;
end //
delimiter ;

drop procedure if exists p_clan_get_wars;
delimiter //
create procedure p_clan_get_wars(varClanId int)
begin
	select war.*, clan1.name as first_clan_name, clan2.name as second_clan_name from war join clan as clan1 on clan1.id = first_clan_id join clan as clan2 on clan2.id = second_clan_id where first_clan_id = varClanId or second_clan_id = varClanId order by date_created desc;
end //
delimiter ;

drop procedure if exists p_player_get_wars;
delimiter //
create procedure p_player_get_wars(varPlayerId int)
begin
	select war.*, clan1.name as first_clan_name, clan2.name as second_clan_name from war_player join war on war.id = war_player.war_id join clan as clan1 on war.first_clan_id = clan1.id join clan as clan2 on war.second_clan_id = clan2.id where player_id = varPlayerId order by date_created desc;
end //
delimiter ;

drop procedure if exists p_get_wars;
delimiter //
create procedure p_get_wars(varPageSize int)
begin
	select war.*, clan1.name as first_clan_name, clan2.name as second_clan_name from war join clan as clan1 on war.first_clan_id = clan1.id join clan as clan2 on war.second_clan_id = clan2.id order by date_created desc limit varPageSize;
end //
delimiter ;

drop procedure if exists p_get_players_and_clans_from_tags;
delimiter //
create procedure p_get_players_and_clans_from_tags(in varTags varchar(1000))
begin
  set @st := concat('select player.*, clan_member.clan_id, rank from player left join clan_member on player.id = clan_member.player_id where clan_member.rank != 5 and tag in ', varTags, ';');
	prepare stmt from @st;
	execute stmt;
end //
delimiter ;

drop table proxy_requests;