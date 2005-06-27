/*
 * Returns tree information from the article tree.
 * Tree stored procedures are usually used for tree flydowns, but are sometimes used for the explorer-like folder tree widgit.
 *
 * @param_current_item INTEGER: The current branch to start building tree information from.
 *
 * @returned_row type_article_tree: Contains levelnum, children, articleid, parent, displayorder and title.
 *
 * Returns a set of returned_rows.
 */
DROP TYPE type_article_tree CASCADE;
CREATE TYPE type_article_tree AS (articleid INTEGER, title VARCHAR(255), levelnum INTEGER);

--DROP FUNCTION sp_article_tree(INTEGER);
CREATE OR REPLACE FUNCTION sp_article_tree(INTEGER) RETURNS SETOF type_article_tree AS '
	DECLARE
		param_current_item ALIAS FOR $1;
		local_level INTEGER;
		local_displayorder INTEGER;
		local_parent INTEGER;
		local_numchildren INTEGER;
		local_current_item INTEGER;
		returned_row type_article_tree%ROWTYPE;
	BEGIN
		local_level := 0;
		local_displayorder := 0;
		CREATE TEMPORARY TABLE stack (item INTEGER, levelnum INTEGER, parent INTEGER);
		CREATE TEMPORARY TABLE out (item INTEGER, displayorder INTEGER, levelnum INTEGER, children INTEGER, parent INTEGER);
		-- Insert the current item on the stack. This is the root of the tree.
		INSERT INTO stack VALUES (param_current_item, local_level, 0);

		WHILE local_level >= 0 LOOP
			BEGIN
				-- This checks if any records exist on the stack with the specified levelnum. The result set is discarded.
				PERFORM item FROM stack WHERE levelnum = local_level;
				IF FOUND THEN
					BEGIN
						-- Get current item and parent.
						SELECT INTO local_current_item, local_parent item, parent FROM stack WHERE levelnum = local_level;
						IF local_current_item = 0 THEN
							DELETE FROM stack WHERE levelnum = local_level;
						ELSE
							DELETE FROM stack WHERE levelnum = local_level AND item = local_current_item;
						END IF;

						-- Insert new rows on the stack for the current item''s children.
						INSERT INTO stack SELECT articleid, local_level + 1, parent
						FROM articles
						INNER JOIN article_parent ON articleid = article
						WHERE parent = local_current_item
						ORDER BY displayorder ASC, title ASC;

						GET DIAGNOSTICS local_numchildren = ROW_COUNT;

						-- Insert current item into the output table.
						INSERT INTO out (item, displayorder, levelnum, children, parent)
						VALUES (local_current_item, local_displayorder, local_level, local_numchildren, local_parent);

						local_displayorder := local_displayorder + 1;
						-- The current item has children. Go to the next level.
						IF local_numchildren > 0 THEN
							local_level := local_level + 1;
						END IF;
					END;
				ELSE
					-- No more records exist at this level. Go back a level.
					local_level := local_level - 1;
				END IF;
			END;
		END LOOP;

		-- Return result set.
		FOR returned_row IN
		SELECT articleid, title, out.levelnum
		FROM out
		INNER JOIN articles ON articles.articleid = item
		INNER JOIN article_parent ON articleid = article
		ORDER BY out.displayorder, article_parent.displayorder, articles.title
		LOOP
			RETURN NEXT returned_row;
		END LOOP;

		-- Drop temporaty tables.
		DROP TABLE stack;
		DROP TABLE out;

		RETURN;
	END;
' LANGUAGE 'plpgsql';
