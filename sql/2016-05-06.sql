alter table player add rank_attacked int default 0;
alter table player add rank_defended int default 0;

drop procedure if exists p_war_get_attacks;
delimiter //
create procedure p_war_get_attacks(varWarId int)
  begin
    select war_attack.*, attacker.rank as attacker_rank, defender.rank as defender_rank
    from war_attack
      join war_player as attacker on attacker_id = attacker.player_id and attacker.war_id = war_attack.war_id
      join war_player as defender on defender_id = defender.player_id and defender.war_id = war_attack.war_id
    where war_attack.war_id = varWarId
    group by attacker_id, defender_id
    order by war_attack.date_created;
  end //
delimiter ;

drop procedure if exists p_player_get_attacks;
delimiter //
create procedure p_player_get_attacks(varPlayerId int)
  begin
    select war_attack.*, attacker.rank as attacker_rank, defender.rank as defender_rank
    from war_attack
      join war_player as attacker on attacker_id = attacker.player_id and attacker.war_id = war_attack.war_id
      join war_player as defender on defender_id = defender.player_id and defender.war_id = war_attack.war_id
    where war_attack.attacker_id = varPlayerId
    group by attacker_id, defender_id
    order by war_attack.date_created;
  end //
delimiter ;

drop procedure if exists p_player_get_defences;
delimiter //
create procedure p_player_get_defences(varPlayerId int)
  begin
    select war_attack.*, attacker.rank as attacker_rank, defender.rank as defender_rank
    from war_attack
      join war_player as attacker on attacker_id = attacker.player_id and attacker.war_id = war_attack.war_id
      join war_player as defender on defender_id = defender.player_id and defender.war_id = war_attack.war_id
    where war_attack.defender_id = varPlayerId
    group by attacker_id, defender_id
    order by war_attack.date_created;
  end //
delimiter ;

alter table clan add first_attack_weight float default 100;
alter table clan add second_attack_weight float default 100;
alter table clan add total_stars_weight float default 100;
alter table clan add new_stars_weight float default 100;
alter table clan add defence_weight float default 100;
alter table clan add number_of_defences_weight float default 100;
alter table clan add attacks_used_weight float default 100;
alter table clan add rank_attacked_weight float default 100;
alter table clan add rank_defended_weight float default 100;
