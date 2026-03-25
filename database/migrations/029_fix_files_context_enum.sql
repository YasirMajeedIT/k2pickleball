-- Migration 029: Expand files.context enum to include 'facility' and 'general'
-- The context enum previously only had avatar/document/logo/attachment/import/export,
-- causing facility image uploads to fail silently (context='facility' rejected by MySQL strict mode).
ALTER TABLE files
    MODIFY COLUMN context ENUM('avatar','document','logo','attachment','import','export','facility','general') DEFAULT 'attachment';
