drop procedure if exists p_player_update_bulk;
delimiter //
create procedure p_player_update_bulk(varId int, varRank varchar(2), varLevel int, varTrophies int, varDonations int, varReceived int, varLeagueUrl varchar(200), varDate datetime, varName varchar(50))
begin
    update player set name=varName, level=varLevel, trophies=varTrophies, donations=varDonations, received=varReceived, league_url=varLeagueUrl where id = varId;
    update clan_member set rank=varRank where player_id = varId and rank != 5;
    if (varLevel <> (select stat_amount from player_stats where player_id = varId and stat_type = 'LV' order by date_recorded desc limit 1)
        or not exists (select * from player_stats where player_id = varId and stat_type = 'LV' limit 1))
        then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'LV', varLevel);
    end if;
    if (varTrophies <> (select stat_amount from player_stats where player_id = varId and stat_type = 'TR' order by date_recorded desc limit 1)
        or not exists (select * from player_stats where player_id = varId and stat_type = 'TR' limit 1))
        then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'TR', varTrophies);
    end if;
    if (varDonations <> (select stat_amount from player_stats where player_id = varId and stat_type = 'DO' order by date_recorded desc limit 1)
        or not exists (select * from player_stats where player_id = varId and stat_type = 'DO' limit 1))
        then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'DO', varDonations);
    end if;
    if (varReceived <> (select stat_amount from player_stats where player_id = varId and stat_type = 'RE' order by date_recorded desc limit 1)
        or not exists (select * from player_stats where player_id = varId and stat_type = 'RE' limit 1))
        then insert into player_stats (player_id, date_recorded, stat_type, stat_amount) values (varId, varDate, 'RE', varReceived);
    end if;
end //
delimiter ;

drop procedure if exists p_clan_update_bulk;
delimiter //
create procedure p_clan_update_bulk(varId int, varName varchar(50), varType varchar(2), varDescription varchar(256), varFrequency varchar(2), varMinTrophies int, varMembers int, varClanPoints int, varClanLevel int, varWarWins int, varBadgeUrl varchar(200), varLocation varchar(50), varDateModified datetime, varHourAgo datetime)
    begin
        update clan set name=varName, clan_type=varType, description=varDescription, war_frequency=varFrequency, minimum_trophies=varMinTrophies, members=varMembers, clan_points=varClanPoints, clan_level=varClanLevel, war_wins=varWarWins, badge_url=varBadgeUrl, location=varLocation, date_modified=varDateModified where id = varId;
        if (varClanPoints <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'CP' order by date_recorded desc limit 1)
            or not exists (select * from clan_stats where clan_id = varId and stat_type = 'CP' limit 1))
        then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CP', varClanPoints);
        end if;
        if (varClanLevel <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'CL' order by date_recorded desc limit 1)
            or not exists (select * from clan_stats where clan_id = varId and stat_type = 'CL' limit 1))
        then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'CL', varClanLevel);
        end if;
        if (varMembers <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'ME' order by date_recorded desc limit 1)
            or not exists (select * from clan_stats where clan_id = varId and stat_type = 'ME' limit 1))
        then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'ME', varMembers);
        end if;
        if (varWarWins <> (select stat_amount from clan_stats where clan_id = varId and stat_type = 'WW' order by date_recorded desc limit 1)
            or not exists (select * from clan_stats where clan_id = varId and stat_type = 'WW' limit 1))
        then insert into clan_stats (clan_id, date_recorded, stat_type, stat_amount) values (varId, varDateModified, 'WW', varWarWins);
        end if;
    end //
delimiter ;