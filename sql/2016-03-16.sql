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