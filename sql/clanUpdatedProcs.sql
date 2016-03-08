drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime)
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified where id = varId;
end //
delimiter ;

drop procedure if exists p_clan_update_player_war_rank;
delimiter //
create procedure p_clan_update_player_war_rank(varClanId int, varPlayerId int, varWarRank int, varDateModified datetime)
begin
	update clan_member set war_rank = varWarRank, date_modified = varDateModified where clan_id = varClanId and player_id = varPlayerId;
end //
delimiter ;