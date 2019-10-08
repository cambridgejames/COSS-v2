-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2019-10-09 02:33:08
-- 服务器版本： 8.0.15
-- PHP 版本： 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `compsys_info`
--

-- --------------------------------------------------------

--
-- 表的结构 `competitor_score`
--

CREATE TABLE `competitor_score` (
  `comps_name` varchar(40) NOT NULL COMMENT '活动名称',
  `player_name` varchar(40) NOT NULL COMMENT '选手姓名',
  `player_group` varchar(20) NOT NULL COMMENT '选手组别',
  `work_name` varchar(40) NOT NULL COMMENT '作品名称',
  `judge_name` varchar(20) NOT NULL COMMENT '评分人',
  `score_detailed` text NOT NULL COMMENT '详细得分',
  `score_sum` smallint(6) NOT NULL DEFAULT '0' COMMENT '总分'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='选手得分表';

-- --------------------------------------------------------

--
-- 表的结构 `comps_info`
--

CREATE TABLE `comps_info` (
  `comps_id` int(11) NOT NULL COMMENT 'ID',
  `comps_name` varchar(40) NOT NULL COMMENT '活动名称',
  `time_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '起始时间',
  `time_duration` smallint(6) NOT NULL COMMENT '持续时间',
  `works_number` int(11) NOT NULL DEFAULT '0' COMMENT '作品数量',
  `competitor_info` text NOT NULL COMMENT '选手信息',
  `score_rubric` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评分细则',
  `isstart_sign` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开始标志'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='竞赛信息表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `comps_info`
--

INSERT INTO `comps_info` (`comps_id`, `comps_name`, `time_duration`, `works_number`, `competitor_info`, `score_rubric`, `isstart_sign`) VALUES
(1, '竞赛在线评分系统Beta2.0-测试', 7200, 5, '组1@g@player1@r@work1@r@player2@r@work2@r@player3@r@work3@r@player4@r@work4@r@player5@r@work5@g@组2@g@player1@r@work6@r@player2@r@work7@r@player3@r@work8@r@player4@r@work9@r@player5@r@work10@g@', '组1@g@细则1@r@0@s@20@r@细则2@r@0@s@40@r@细则3@r@0@s@40@g@组2@g@细则4@r@0@s@10@r@细则5@r@0@s@30@r@细则6@r@0@s@60', 0);

-- --------------------------------------------------------

--
-- 表的结构 `users_info`
--

CREATE TABLE `users_info` (
  `users_id` int(11) NOT NULL COMMENT 'ID',
  `users_nickname` varchar(11) NOT NULL COMMENT '用户名',
  `users_phone` char(11) DEFAULT NULL COMMENT '手机',
  `users_mailbox` varchar(40) DEFAULT NULL COMMENT '邮箱',
  `users_pwdhash` char(60) NOT NULL COMMENT '密码（hash）',
  `users_authority` tinyint(4) NOT NULL DEFAULT '5' COMMENT '权限',
  `comps_name` varchar(40) DEFAULT NULL COMMENT '活动名称',
  `works_number` int(11) NOT NULL DEFAULT '0' COMMENT '作品数量',
  `reviewed_number` int(11) NOT NULL DEFAULT '0' COMMENT '已评数量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户信息表';

--
-- 转存表中的数据 `users_info`
--

INSERT INTO `users_info` (`users_id`, `users_nickname`, `users_phone`, `users_mailbox`, `users_pwdhash`, `users_authority`, `comps_name`, `works_number`, `reviewed_number`) VALUES
(1, 'root', NULL, 'cambridge_james@foxmail.com', '$2y$10$zPbIeLyyjWgYg9F8XoTptOhHk4DXkgkX2m7gh3bjk5bDx20femlSa', 1, '竞赛在线评分系统Beta2.0-测试', 5, 0),
(2, 'judge1', NULL, '', '$2y$10$Ju6pa6RM2hvWn9g8qCUyr.Bv7SseL4sjt5nF1WsidgyGEFtAXms02', 4, '竞赛在线评分系统Beta2.0-测试', 5, 0),
(3, 'judge2', NULL, '', '$2y$10$i.QIdwBOhOhcdRWf5Uzb7.G0ot9EKGM/8I4zHbHYIt1PqnUbwrlM2', 4, '竞赛在线评分系统Beta2.0-测试', 5, 0);

--
-- 转储表的索引
--

--
-- 表的索引 `comps_info`
--
ALTER TABLE `comps_info`
  ADD PRIMARY KEY (`comps_id`);

--
-- 表的索引 `users_info`
--
ALTER TABLE `users_info`
  ADD PRIMARY KEY (`users_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `comps_info`
--
ALTER TABLE `comps_info`
  MODIFY `comps_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `users_info`
--
ALTER TABLE `users_info`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
