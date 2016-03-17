create table clan_stats(
	clan_id int not null,
	date_recorded datetime not null,
	stat_type varchar(2) not null,
	stat_amount int not null,
	foreign key(clan_id) references clan(id)
);

drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200), varDate datetime)
begin
	update player set level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
	update clan_member set rank=varRank where player_id = varId and rank != 'KI' and rank != 'EX';
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'LV', varLevel);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'TR', varTrophies);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'DO', varDonations);
	insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'RE', varReceived);
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime, varApiInfo varchar(50000), varDate datetime)
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified, api_info=varApiInfo where id = varId;
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'CP', varClanPoints);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'CL', varClanLevel);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'ME', varMembers);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'WW', varWarWins);
end //
delimiter ;