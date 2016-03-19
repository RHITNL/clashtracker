drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime, varApiInfo varchar(50000), varHourAgo datetime)
begin
	update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified, api_info=varApiInfo where id = varId;
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CP', varClanPoints);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CL', varClanLevel);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'ME', varMembers);
	insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'WW', varWarWins);
	update clan set api_info = null where api_info is not null and date_modified < varHourAgo;
end //
delimiter ;

drop procedure if exists p_loot_report_create;
delimiter //
create procedure p_loot_report_create(varClanId int, varDate datetime)
begin
	insert into loot_report(clan_id, date_created) values(varClanId, varDate);
	select * from loot_report where id in (select last_insert_id() as id);
end //
delimiter ;

alter table loot_report_player_result add primary key (player_id, loot_report_id, loot_type);

drop procedure if exists p_player_best_report_results;
delimiter //
create procedure p_player_best_report_results(varPlayerId int)
begin
	select loot_type, max(loot_amount) as max from loot_report_player_result where player_id = varPlayerId group by loot_type;
end //
delimiter ;