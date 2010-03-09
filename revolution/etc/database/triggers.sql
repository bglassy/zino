delimiter |

CREATE TRIGGER commentinsert AFTER INSERT ON `comments`
   FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_comments` = `count_comments` + 1 WHERE `count_userid` = NEW.`comment_userid` LIMIT 1;
        CASE NEW.`comment_typeid`
            WHEN 1 THEN UPDATE `polls` SET `poll_numcomments` = `poll_numcomments` + 1 WHERE `poll_id`=NEW.`comment_itemid` LIMIT 1;
            WHEN 2 THEN BEGIN
                UPDATE `images` SET `image_numcomments` = `image_numcomments` + 1 WHERE `image_id`=NEW.`comment_itemid` LIMIT 1;
                UPDATE `albums`, `images` SET `album_numcomments` = `album_numcomments` + 1 WHERE `image_albumid`=`album_id` AND `image_id`=NEW.`comment_itemid` LIMIT 1;
            END;
            WHEN 3 THEN UPDATE `userprofiles` SET `profile_numcomments` = `profile_numcomments` + 1 WHERE `profile_userid`=NEW.`comment_itemid` LIMIT 1;
            WHEN 4 THEN UPDATE `journals` SET `journal_numcomments` = `journal_numcomments` + 1 WHERE `journal_id`=NEW.`comment_itemid` LIMIT 1;
            WHEN 7 THEN UPDATE `schools` SET `school_numcomments` = `school_numcomments` + 1 WHERE `school_id`=NEW.`comment_itemid` LIMIT 1;
        END CASE;
   END;
|

CREATE TRIGGER commentdelete AFTER DELETE ON `comments`
   FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_comments` = `count_comments` - 1 WHERE `count_userid` = OLD.`comment_userid` LIMIT 1;
        CASE OLD.`comment_typeid`
            WHEN 1 THEN UPDATE `polls` SET `poll_numcomments` = `poll_numcomments` - 1 WHERE `poll_id`=OLD.`comment_itemid` LIMIT 1;
            WHEN 2 THEN BEGIN
                UPDATE `images` SET `image_numcomments` = `image_numcomments` - 1 WHERE `image_id`=OLD.`comment_itemid` LIMIT 1;
                UPDATE `albums`, `images` SET `album_numcomments` = `album_numcomments` - 1 WHERE `image_albumid`=`album_id` AND `image_id`=OLD.`comment_itemid` LIMIT 1;
            END;
            WHEN 3 THEN UPDATE `userprofiles` SET `profile_numcomments` = `profile_numcomments` - 1 WHERE `profile_userid`=OLD.`comment_itemid` LIMIT 1;
            WHEN 4 THEN UPDATE `journals` SET `journal_numcomments` = `journal_numcomments` - 1 WHERE `journal_id`=OLD.`comment_itemid` LIMIT 1;
            WHEN 7 THEN UPDATE `schools` SET `school_numcomments` = `school_numcomments` - 1 WHERE `school_id`=OLD.`comment_itemid` LIMIT 1;
        END CASE;
        DELETE FROM `bulk` WHERE `bulk_id`=OLD.`comment_bulkid` LIMIT 1;
   END;
|

CREATE TRIGGER imageinsert AFTER INSERT ON `images`
   FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_images` = `count_images` + 1 WHERE `count_userid` = NEW.`image_userid` LIMIT 1;
        UPDATE `albums` SET `album_numphotos` = `album_numphotos` + 1 WHERE `album_id` = NEW.`image_albumid` LIMIT 1;
   END;
|

CREATE TRIGGER imagedelete AFTER DELETE ON `images`
   FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_images` = `count_images` - 1 WHERE `count_userid` = OLD.`image_userid` LIMIT 1;
        UPDATE `albums` SET `album_numphotos` = `album_numphotos` - 1 WHERE `album_id` = OLD.`image_albumid` LIMIT 1;
        DELETE FROM `comments` WHERE `comment_itemid` = OLD.`image_itemid` AND `comment_typeid` = 2 LIMIT 1;
        DELETE FROM `favourites` WHERE `favourite_itemid` = OLD.`image_itemid` AND `favourite_typeid` = 2 LIMIT 1;
   END;
|

CREATE TRIGGER albuminsert AFTER INSERT ON `albums`
    FOR EACH ROW BEGIN
		IF NEW.`album_ownertype` = 3 THEN
			UPDATE `usercounts` SET `count_albums` = `count_albums` + 1 WHERE `count_userid` = NEW.`album_ownerid` LIMIT 1;
		END IF;
    END;
|

CREATE TRIGGER albumdelete BEFORE DELETE ON `albums`
   FOR EACH ROW BEGIN
        IF OLD.`album_ownertype` = 3 THEN 
			UPDATE `usercounts` SET `count_albums` = `count_albums` - 1 WHERE `count_userid` = OLD.`album_ownerid` LIMIT 1;
		END IF;
        IF OLD.`album_ownertype` = 3 THEN UPDATE `usercounts` SET `count_albums` = `count_albums` - 1 WHERE `count_userid` = OLD.`album_ownerid` LIMIT 1;
			UPDATE `usercounts` SET `count_albums` = `count_albums` - 1 WHERE `count_userid` = OLD.`album_ownerid` LIMIT 1;
		END IF;
        UPDATE `usercounts` SET `count_images` = `count_images` - OLD.`album_numphotos` WHERE `count_userid`=OLD.`album_ownerid` LIMIT 1;
       DELETE FROM `images` WHERE `image_albumid` = OLD.`album_id` LIMIT 1;
   END;
|

CREATE TRIGGER pollinsert AFTER INSERT ON `polls`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_polls` = `count_polls` + 1 WHERE `count_userid` = NEW.`poll_userid` LIMIT 1;
    END;
|

CREATE TRIGGER polldelete AFTER DELETE ON `polls`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_polls` = `count_polls` - 1 WHERE `count_userid` = OLD.`poll_userid` LIMIT 1;
        DELETE FROM `comments` WHERE `comment_itemid` = `image_itemid` AND `comment_typeid` = 1 LIMIT 1;
    END;
|

CREATE TRIGGER journalinsert AFTER INSERT ON `journals`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_journals` = `count_journals` + 1 WHERE `count_userid` = NEW.`journal_userid` LIMIT 1;
    END;
|

CREATE TRIGGER journaldelete AFTER DELETE ON `journals`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_journals` = `count_journals` - 1 WHERE `count_userid` = OLD.`journal_userid` LIMIT 1;
        DELETE FROM `bulk` WHERE `bulk_id` = OLD.`journal_bulkid` LIMIT 1;
        DELETE FROM `comments` WHERE `comment_itemid` = OLD.`journal_itemid` AND `comment_typeid` = 4 LIMIT 1;
        DELETE FROM `favourites` WHERE `favourite_itemid` = OLD.`journal_itemid` AND `favourite_typeid` = 4 LIMIT 1;
    END;
|

CREATE TRIGGER shoutinsert AFTER INSERT ON `shoutbox`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_shouts` = `count_shouts` + 1 WHERE `count_userid` = NEW.`shout_userid` LIMIT 1;
    END;
|

CREATE TRIGGER shoutdelete AFTER DELETE ON `shoutbox`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_shouts` = `count_shouts` - 1 WHERE `count_userid` = OLD.`shout_userid` LIMIT 1;
        DELETE FROM `bulk` WHERE `bulk_id`=OLD.`shout_bulkid` LIMIT 1;
    END;
|

CREATE TRIGGER relationinsert AFTER INSERT ON `relations`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_relations` = `count_relations` + 1 WHERE `count_userid` = NEW.`relation_userid` LIMIT 1;
    END;
|

CREATE TRIGGER relationdelete AFTER DELETE ON `relations`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_relations` = `count_relations` - 1 WHERE `count_userid` = OLD.`relation_userid` LIMIT 1;
    END;
|

CREATE TRIGGER favouriteinsert AFTER INSERT ON `favourites`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_favourites` = `count_favourites` + 1 WHERE `count_userid` = NEW.`favourite_userid` LIMIT 1;
    END;
|

CREATE TRIGGER favouritedelete AFTER DELETE ON `favourites`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_favourites` = `count_favourites` - 1 WHERE `count_userid` = OLD.`favourite_userid` LIMIT 1;
    END;
|

CREATE TRIGGER answerinsert AFTER INSERT ON `answers`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_answers` = `count_answers` + 1 WHERE `count_userid` = NEW.`answer_userid` LIMIT 1;
    END;
|

CREATE TRIGGER answerdelete AFTER DELETE ON `answers`
    FOR EACH ROW BEGIN
        UPDATE `usercounts` SET `count_answers` = `count_answers` - 1 WHERE `count_userid` = OLD.`answer_userid` LIMIT 1;
    END;
|

CREATE TRIGGER questiondelete AFTER DELETE ON `questions`
    FOR EACH ROW BEGIN
        DELETE FROM `answers` WHERE `answer_questionid`=OLD.`question_id`;
    END;
|

CREATE TRIGGER beforebirth BEFORE INSERT ON `users`
    FOR EACH ROW BEGIN
        INSERT INTO `albums` (`album_ownerid`, `album_ownertype`) VALUES (NULL, 3);
        SET NEW.`user_egoalbumid` = LAST_INSERT_ID();
    END;
|

CREATE TRIGGER userbirth AFTER INSERT ON `users`
    FOR EACH ROW BEGIN
        INSERT INTO `usercounts` (`count_userid`) VALUES (NEW.`user_id`);
        UPDATE `albums` SET `album_ownerid` = NEW.`user_id` WHERE `album_id`=NEW.`user_egoalbumid` LIMIT 1;
        INSERT INTO `pmfolders` (`pmfolder_userid`, `pmfolder_name`, `pmfolder_typeid`) VALUES (NEW.`user_id`, 'inbox', 'inbox');
        INSERT INTO `pmfolders` (`pmfolder_userid`, `pmfolder_name`, `pmfolder_typeid`) VALUES (NEW.`user_id`, 'outbox', 'outbox');
    END;
|

CREATE TRIGGER userdeath AFTER DELETE ON `users`
    FOR EACH ROW BEGIN
        DELETE FROM `usercounts` WHERE `count_userid`=OLD.`user_id`;
        DELETE FROM `userprofiles` WHERE `profile_userid`=OLD.`user_id`;
        DELETE FROM `albums` WHERE `album_userid`=OLD.`user_id`;
        DELETE FROM `comments` WHERE `comment_userid`=OLD.`user_id`;
        DELETE FROM `polls` WHERE `poll_userid`=OLD.`user_id`;
        DELETE FROM `journals` WHERE `journal_userid`=OLD.`user_id`;
        DELETE FROM `pmfolders` WHERE `pmfolder_userid`=OLD.`user_id`;                                                                                                 
	END;
|