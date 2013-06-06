<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Initial setup of the needed tables for TYPO3.Docs
 */
class Version20130606131228 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE TABLE typo3_docs_domain_model_author (persistence_object_identifier VARCHAR(40) NOT NULL, PRIMARY KEY(persistence_object_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_domain_model_author_documents_join (docs_author VARCHAR(40) NOT NULL, docs_document VARCHAR(40) NOT NULL, INDEX IDX_36F0E20C35A7C08C (docs_author), INDEX IDX_36F0E20C5F101029 (docs_document), PRIMARY KEY(docs_author, docs_document)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_domain_model_category (persistence_object_identifier VARCHAR(40) NOT NULL, parent VARCHAR(40) DEFAULT NULL, title VARCHAR(100) NOT NULL, INDEX IDX_6E0952023D8E604F (parent), PRIMARY KEY(persistence_object_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_domain_model_category_documents_join (docs_category VARCHAR(40) NOT NULL, docs_document VARCHAR(40) NOT NULL, INDEX IDX_98E74D238135839E (docs_category), INDEX IDX_98E74D235F101029 (docs_document), PRIMARY KEY(docs_category, docs_document)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_domain_model_document (persistence_object_identifier VARCHAR(40) NOT NULL, title VARCHAR(255) NOT NULL, abstract LONGTEXT NOT NULL, type VARCHAR(100) NOT NULL, version VARCHAR(30) NOT NULL, status ENUM('ok', 'documentation-not-found', 'ok-with-warnings', 'error-parsing', 'waiting-rendering', 'waiting-sync'), generationdate DATETIME NOT NULL, locale VARCHAR(50) NOT NULL, product VARCHAR(20) NOT NULL, packagekey VARCHAR(100) NOT NULL, uri VARCHAR(255) NOT NULL, urialias VARCHAR(255) NOT NULL, repository VARCHAR(255) NOT NULL, repositorytag VARCHAR(255) NOT NULL, repositorytype VARCHAR(100) NOT NULL, packagefile VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_B02CC1B5841CB121 (uri), PRIMARY KEY(persistence_object_identifier)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_domain_model_document_authors_join (docs_document VARCHAR(40) NOT NULL, docs_author VARCHAR(40) NOT NULL, INDEX IDX_3AB456985F101029 (docs_document), INDEX IDX_3AB4569835A7C08C (docs_author), PRIMARY KEY(docs_document, docs_author)) ENGINE = InnoDB");
		$this->addSql("CREATE TABLE typo3_docs_domain_model_document_categories_join (docs_document VARCHAR(40) NOT NULL, docs_category VARCHAR(40) NOT NULL, INDEX IDX_2730635F5F101029 (docs_document), INDEX IDX_2730635F8135839E (docs_category), PRIMARY KEY(docs_document, docs_category)) ENGINE = InnoDB");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_author ADD CONSTRAINT FK_97B4B68A47A46B0A FOREIGN KEY (persistence_object_identifier) REFERENCES typo3_party_domain_model_abstractparty (persistence_object_identifier) ON DELETE CASCADE");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_author_documents_join ADD CONSTRAINT FK_36F0E20C35A7C08C FOREIGN KEY (docs_author) REFERENCES typo3_docs_domain_model_author (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_author_documents_join ADD CONSTRAINT FK_36F0E20C5F101029 FOREIGN KEY (docs_document) REFERENCES typo3_docs_domain_model_document (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_category ADD CONSTRAINT FK_6E0952023D8E604F FOREIGN KEY (parent) REFERENCES typo3_docs_domain_model_category (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_category_documents_join ADD CONSTRAINT FK_98E74D238135839E FOREIGN KEY (docs_category) REFERENCES typo3_docs_domain_model_category (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_category_documents_join ADD CONSTRAINT FK_98E74D235F101029 FOREIGN KEY (docs_document) REFERENCES typo3_docs_domain_model_document (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_document_authors_join ADD CONSTRAINT FK_3AB456985F101029 FOREIGN KEY (docs_document) REFERENCES typo3_docs_domain_model_document (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_document_authors_join ADD CONSTRAINT FK_3AB4569835A7C08C FOREIGN KEY (docs_author) REFERENCES typo3_docs_domain_model_author (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_document_categories_join ADD CONSTRAINT FK_2730635F5F101029 FOREIGN KEY (docs_document) REFERENCES typo3_docs_domain_model_document (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_document_categories_join ADD CONSTRAINT FK_2730635F8135839E FOREIGN KEY (docs_category) REFERENCES typo3_docs_domain_model_category (persistence_object_identifier)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("ALTER TABLE typo3_docs_domain_model_author_documents_join DROP FOREIGN KEY FK_36F0E20C35A7C08C");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_document_authors_join DROP FOREIGN KEY FK_3AB4569835A7C08C");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_category DROP FOREIGN KEY FK_6E0952023D8E604F");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_category_documents_join DROP FOREIGN KEY FK_98E74D238135839E");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_document_categories_join DROP FOREIGN KEY FK_2730635F8135839E");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_author_documents_join DROP FOREIGN KEY FK_36F0E20C5F101029");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_category_documents_join DROP FOREIGN KEY FK_98E74D235F101029");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_document_authors_join DROP FOREIGN KEY FK_3AB456985F101029");
		$this->addSql("ALTER TABLE typo3_docs_domain_model_document_categories_join DROP FOREIGN KEY FK_2730635F5F101029");
		$this->addSql("DROP TABLE typo3_docs_domain_model_author");
		$this->addSql("DROP TABLE typo3_docs_domain_model_author_documents_join");
		$this->addSql("DROP TABLE typo3_docs_domain_model_category");
		$this->addSql("DROP TABLE typo3_docs_domain_model_category_documents_join");
		$this->addSql("DROP TABLE typo3_docs_domain_model_document");
		$this->addSql("DROP TABLE typo3_docs_domain_model_document_authors_join");
		$this->addSql("DROP TABLE typo3_docs_domain_model_document_categories_join");
	}
}

?>