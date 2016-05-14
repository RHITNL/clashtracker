create table proxy_requests(
  id int auto_increment not null,
  proxy varchar(50),
  request_url varchar(50) not null,
  response varchar(50000) not null,
  ip varchar (20) not null,
  auth varchar(100) not null,
  date_requested datetime not null,
  primary key(id)
);

drop procedure if exists p_proxy_request_create;
delimiter //
create procedure p_proxy_request_create(varProxy varchar(50), varRequestUrl varchar(50), varResponse varchar(50000), varIp varchar(20), varAuth varchar(100), varDate datetime)
  BEGIN
    INSERT INTO proxy_requests(proxy, request_url, response, ip, auth, date_requested) values(varProxy, varRequestUrl, varResponse, varIp, varAuth, varDate);
  END //
delimiter ;