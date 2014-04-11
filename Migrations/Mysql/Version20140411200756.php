<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Set up initial schema
 */
class Version20140411200756 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE TABLE typo3_docs_renderinghub_domain_model_author (persistence_object_identifier VARCHAR(40) NOT NULL, PRIMARY KEY(persistence_object_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_renderinghub_domain_model_author_documents_join (renderinghub_author VARCHAR(40) NOT NULL, renderinghub_document VARCHAR(40) NOT NULL, INDEX IDX_EDE6E4A5729A4CF1 (renderinghub_author), INDEX IDX_EDE6E4A5218F9FA4 (renderinghub_document), PRIMARY KEY(renderinghub_author, renderinghub_document)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_renderinghub_domain_model_category (persistence_object_identifier VARCHAR(40) NOT NULL, parent VARCHAR(40) DEFAULT NULL, title VARCHAR(100) NOT NULL, INDEX IDX_413296BE3D8E604F (parent), PRIMARY KEY(persistence_object_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_renderinghub_domain_model_category_documents_join (renderinghub_category VARCHAR(40) NOT NULL, renderinghub_document VARCHAR(40) NOT NULL, INDEX IDX_E4134A0FFAA0C13 (renderinghub_category), INDEX IDX_E4134A0218F9FA4 (renderinghub_document), PRIMARY KEY(renderinghub_category, renderinghub_document)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_renderinghub_domain_model_document (persistence_object_identifier VARCHAR(40) NOT NULL, title VARCHAR(255) NOT NULL, abstract LONGTEXT NOT NULL, type VARCHAR(100) NOT NULL, version VARCHAR(30) NOT NULL, status ENUM('ok', 'documentation-not-found', 'ok-with-warnings', 'error-parsing', 'waiting-rendering', 'waiting-sync'), generationdate DATETIME NOT NULL, locale VARCHAR(50) NOT NULL, product VARCHAR(20) NOT NULL, packagekey VARCHAR(100) NOT NULL, uri VARCHAR(255) NOT NULL, urialias VARCHAR(255) NOT NULL, repository VARCHAR(255) NOT NULL, repositorytag VARCHAR(255) NOT NULL, repositorytype VARCHAR(100) NOT NULL, packagefile VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_9F170509841CB121 (uri), PRIMARY KEY(persistence_object_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_renderinghub_domain_model_document_authors_join (renderinghub_document VARCHAR(40) NOT NULL, renderinghub_author VARCHAR(40) NOT NULL, INDEX IDX_E1A25031218F9FA4 (renderinghub_document), INDEX IDX_E1A25031729A4CF1 (renderinghub_author), PRIMARY KEY(renderinghub_document, renderinghub_author)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_renderinghub_domain_model_document_categories_join (renderinghub_document VARCHAR(40) NOT NULL, renderinghub_category VARCHAR(40) NOT NULL, INDEX IDX_531717BC218F9FA4 (renderinghub_document), INDEX IDX_531717BCFFAA0C13 (renderinghub_category), PRIMARY KEY(renderinghub_document, renderinghub_category)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_renderinghub_domain_model_package (title VARCHAR(150) NOT NULL, abstract LONGTEXT NOT NULL, product VARCHAR(30) NOT NULL, locale VARCHAR(30) NOT NULL, type VARCHAR(30) NOT NULL, packagekey VARCHAR(100) NOT NULL, uri VARCHAR(255) NOT NULL, repository VARCHAR(255) NOT NULL, version VARCHAR(100) NOT NULL, repositorytype VARCHAR(30) NOT NULL, repositorytag VARCHAR(100) NOT NULL) ENGINE = InnoDB");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_author ADD CONSTRAINT FK_F1D8A37C47A46B0A FOREIGN KEY (persistence_object_identifier) REFERENCES typo3_party_domain_model_abstractparty (persistence_object_identifier) ON DELETE CASCADE");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_author_documents_join ADD CONSTRAINT FK_EDE6E4A5729A4CF1 FOREIGN KEY (renderinghub_author) REFERENCES typo3_docs_renderinghub_domain_model_author (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_author_documents_join ADD CONSTRAINT FK_EDE6E4A5218F9FA4 FOREIGN KEY (renderinghub_document) REFERENCES typo3_docs_renderinghub_domain_model_document (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_category ADD CONSTRAINT FK_413296BE3D8E604F FOREIGN KEY (parent) REFERENCES typo3_docs_renderinghub_domain_model_category (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_category_documents_join ADD CONSTRAINT FK_E4134A0FFAA0C13 FOREIGN KEY (renderinghub_category) REFERENCES typo3_docs_renderinghub_domain_model_category (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_category_documents_join ADD CONSTRAINT FK_E4134A0218F9FA4 FOREIGN KEY (renderinghub_document) REFERENCES typo3_docs_renderinghub_domain_model_document (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_document_authors_join ADD CONSTRAINT FK_E1A25031218F9FA4 FOREIGN KEY (renderinghub_document) REFERENCES typo3_docs_renderinghub_domain_model_document (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_document_authors_join ADD CONSTRAINT FK_E1A25031729A4CF1 FOREIGN KEY (renderinghub_author) REFERENCES typo3_docs_renderinghub_domain_model_author (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_document_categories_join ADD CONSTRAINT FK_531717BC218F9FA4 FOREIGN KEY (renderinghub_document) REFERENCES typo3_docs_renderinghub_domain_model_document (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_document_categories_join ADD CONSTRAINT FK_531717BCFFAA0C13 FOREIGN KEY (renderinghub_category) REFERENCES typo3_docs_renderinghub_domain_model_category (persistence_object_identifier)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_author_documents_join DROP FOREIGN KEY FK_EDE6E4A5729A4CF1");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_document_authors_join DROP FOREIGN KEY FK_E1A25031729A4CF1");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_category DROP FOREIGN KEY FK_413296BE3D8E604F");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_category_documents_join DROP FOREIGN KEY FK_E4134A0FFAA0C13");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_document_categories_join DROP FOREIGN KEY FK_531717BCFFAA0C13");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_author_documents_join DROP FOREIGN KEY FK_EDE6E4A5218F9FA4");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_category_documents_join DROP FOREIGN KEY FK_E4134A0218F9FA4");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_document_authors_join DROP FOREIGN KEY FK_E1A25031218F9FA4");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_document_categories_join DROP FOREIGN KEY FK_531717BC218F9FA4");
		$this->addSql("DROP TABLE typo3_docs_renderinghub_domain_model_author");
		$this->addSql("DROP TABLE typo3_docs_renderinghub_domain_model_author_documents_join");
		$this->addSql("DROP TABLE typo3_docs_renderinghub_domain_model_category");
		$this->addSql("DROP TABLE typo3_docs_renderinghub_domain_model_category_documents_join");
		$this->addSql("DROP TABLE typo3_docs_renderinghub_domain_model_document");
		$this->addSql("DROP TABLE typo3_docs_renderinghub_domain_model_document_authors_join");
		$this->addSql("DROP TABLE typo3_docs_renderinghub_domain_model_document_categories_join");
		$this->addSql("DROP TABLE typo3_docs_renderinghub_domain_model_package");
	}
}