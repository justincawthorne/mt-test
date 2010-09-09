-- phpMyAdmin SQL Dump
-- version 2.11.10
-- http://www.phpmyadmin.net
--
-- Host: internal-db.s103224.gridserver.com
-- Generation Time: Sep 05, 2010 at 11:17 PM
-- Server version: 5.1.26
-- PHP Version: 4.4.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db103224_wickedwords`
--

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `url`, `summary`, `body`, `category_id`, `author_id`, `status`, `date_uploaded`, `date_amended`, `seo_title`, `seo_desc`, `seo_keywords`, `view_count`, `visit_count`, `comments_disable`, `comments_hide`) VALUES
(101, 'Author article', 'editor-article', '', '<p>The prime minister\\''s media adviser, <a title=\\"More from guardian.co.uk on Andy Coulson\\" href=\\"http://www.guardian.co.uk/media/andy-coulson\\">Andy Coulson</a>, freely discussed the use of unlawful news-gathering techniques while editor of the <a title=\\"More from guardian.co.uk on News of the World\\" href=\\"http://www.guardian.co.uk/media/newsoftheworld\\">News of the World</a> and \\"actively encouraged\\" a named reporter to engage in the illegal interception of voicemail messages, <a href=\\"http://www.nytimes.com/2010/09/05/magazine/05hacking-t.html?ref=global-home\\">according to allegations published by the New York Times</a>.</p>\r\n<p>Coulson,  who resigned as editor of the News of the World in January 2007 after  its royal correspondent was jailed for intercepting voicemail messages,  has always insisted that he had no knowledge of illegal activity when he  edited the paper or at any time as a journalist. He told a Commons  select committee last year: \\"I have never had any involvement in it at  all.\\"</p>\r\n<p>The <a title=\\"More from guardian.co.uk on New York Times\\" href=\\"http://www.guardian.co.uk/media/new-york-times\\">New York Times</a> website  published a trail to a story due to appear in its Sunday  magazine. It made detailed allegations likely to bring intense new  pressure on Coulson and the Metropolitan police force, which stands  accused of favouring <a title=\\"More from guardian.co.uk on Rupert Murdoch\\" href=\\"http://www.guardian.co.uk/media/rupert-murdoch\\">Rupert Murdoch</a>\\''s  newspaper group by cutting short its investigation, withholding crucial  evidence from prosecutors and failing to inform victims of the  newspaper\\''s crimes against them. Coulson declined to comment on the  allegations. The News of the World and Scotland Yard have denied all the  charges.</p>', 2, 1, 'P', '2010-09-01 16:58:00', '2010-09-01 17:02:02', '', '', '', 1, 1, 0, 0),
(102, 'Editor article', 'editor-article_2', 'Subscription-based Sony Qriocity service to be available in UK though PlayStation 3s and other Sony devices this year', '<p><a title=\\"More from guardian.co.uk on Sony\\" href=\\"http://www.guardian.co.uk/technology/sony\\">Sony</a> has embarked on an ambitious challenge to Apple\\''s <a title=\\"More from guardian.co.uk on itunes\\" href=\\"http://www.guardian.co.uk/technology/itunes\\">iTunes</a>, promising to launch a music and video streaming service in the UK by the end of this year.</p>\r\n<p>The  subscription-based service is to be based around the PlayStation 3  console. Sony said that customers would be able to download  high-definition movies and songs over the internet and watch them on  other web-enabled Sony devices, including its TVs, laptops and digital  music players.</p>\r\n<p>With Amazon also thought to be aggressively  planning a web-based subscription service, which would stream old films  and TV shows, the <a title=\\"More from guardian.co.uk on Online TV\\" href=\\"http://www.guardian.co.uk/media/online-tv\\">online TV</a>-on-demand market is about to expand dramatically.</p>\r\n<p>The  Japanese electronics giant revealed its plans in Berlin today at the  start of IFA, Europe\\''s biggest consumer electronics show, shortly before  Apple was scheduled to make its own announcement in San Francisco</p>', 1, 2, 'P', '2010-09-01 17:05:00', '2010-09-02 00:24:44', '', '', '', 1, 1, 0, 0),
(103, 'Contributor article', 'contributor-article', 'The Deep has combined soapy relationship storylines with submarine drama - and produced oddly likeable results', '<p>Initially, as The Deep\\''s characters were introduced and their  soap-opera relationship issues revealed, I\\''ll admit to a sinking  feeling. If there was one thing we learned from the BBC\\''s ill-fated  ferry-based drama <a title=\\"Triangle\\" href=\\"http://www.youtube.com/watch?v=_-gmAloZDE8\\">Triangle</a> it\\''s that soap and water don\\''t always mix. But while I couldn\\''t care  less about the characters\\'' torturous dilemmas on dry land, or even at  sea level, stick them in a metal environment and sink them to the highly  pressurised and dangerous depths of the ocean and I\\''ll automatically  give a damn.</p>\r\n<p>It\\''s the setting that\\''s been the star of the show  here, with the cold, lonely and deadly atmospherics of the deep Arctic  ocean given centre stage (although the addition of Peep Show\\''s  Ukraine-born Vera Filatova and ER\\''s Goran Visnjic have also given the  cast a nicely international feel). You just know there will be scenes  where glass cracks and bolts creak as they reach depths their vessel  wasn\\''t designed to tolerate, as in <a title=\\"Das Boot\\" href=\\"http://www.youtube.com/watch?v=Yo67TAEOUnc\\">Das Boot</a>. But why not? They always work. And you\\''d definitely miss them if they weren\\''t there.</p>', 3, 3, 'P', '2010-09-01 17:50:00', '2010-09-01 17:51:41', '', '', '', 0, 0, 0, 0),
(104, 'New cont article', 'new-cont-article', 'The Bill should have one more investigation - into their own murder, by axe, from above', '<p>So farewell then, <strong>The Bill </strong>(ITV). Sergeant Stone  finally proves himself, Jasmine finally sings, the gang goes down. And  DCI Jack Meadows holds forth on the the subject of respect. Somewhere  along the line, someone changed the meaning of the word. You earn  respect now through power, fear, money, the blade of a knife. How did  that happen? Respect should be what his officers deserve: for having  guns pointed at their heads by teenage thugs, then turning up to work  the following morning at 7am sharp. Jack is proud of his team, and of  the job they do every day. \\"It\\''s an honour.\\"</p>\r\n<p>And it\\''s been an  honour to be part  of it, Jack. Sir. He\\''s talking to the team, and to  the press, after the successful culmination of the Liam Martin murder  case, and the subsequent rape of Jasmine Harris. Maybe he\\''s also   talking to the ITV top brass, after their decision to axe his show.  Where\\''s the respect there?</p>\r\n<p>Perhaps there should have been one  further case, an investigation into their own murder, by axe, from  above. Smithy and Stone, using their newly discovered cooperativeness,  could maybe interview ITV <a title=\\"More from guardian.co.uk on Drama\\" href=\\"http://www.guardian.co.uk/tv-and-radio/drama\\">drama</a> boss Laura Mackie, giving it the old good-cop-bad-cop routine (Stone  would obviously be the bad cop). DC Grace Dasari could do some of the  cleverer stuff, profiling etc. She could look back over the show\\''s  27-year history, investigate how tastes have changed over time, try to  figure out why revamps and slot switches have failed to halt a slide in  ratings</p>', 4, 3, 'P', '2010-09-01 18:07:00', '2010-09-01 18:08:44', '', '', '', 1, 1, 0, 0);

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id`, `name`, `url`, `summary`, `biography`, `email`, `pass`, `guest_flag`, `guest_areas`, `sub_expiry`, `last_login`, `last_ip`, `last_sess`, `image`, `contact_flag`) VALUES
(1, 'Justin Author', 'justin-author', 'testing changed details AGAIN!', '', 'justincawthorne@gmail.com', 'author', 0, 'author', '0000-00-00 00:00:00', '2010-09-05 05:20:01', '58.7.233.139', '672051027026e5287fff64b076ad142a', NULL, 1),
(2, 'Justin Editor', 'justin-editor', 'editor summary', 'editor bio', 'editor@gmail.com', 'editor', 1, 'editor', '0000-00-00 00:00:00', '2010-09-01 19:40:08', '58.7.233.139', 'dc5e27c03c67a7d748a1c69cf30211cb', NULL, 1),
(3, 'Justin Contributor', 'justin-contributor', 'contributor summary', 'contributor bio', 'cont@gmail.com', 'cont', 1, 'contributor', '0000-00-00 00:00:00', '2010-09-01 17:50:36', '58.7.233.139', '248c4c6cec19a28a605cfee6b36e146f', NULL, 0);

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_id`, `title`, `url`, `summary`, `description`, `type`) VALUES
(1, 0, 'editor category', 'editor-category', NULL, NULL, NULL),
(2, 0, 'author category', 'author-category', NULL, NULL, NULL),
(3, 0, 'contributor category', 'contributor-category', NULL, NULL, NULL),
(4, 0, 'test category', 'test-category', NULL, NULL, NULL);

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `title`, `url`, `summary`) VALUES
(2, 'testing', 'testing', NULL),
(3, 'development', 'development', NULL);
