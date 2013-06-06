<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Initial setup of the needed tables for TYPO3.Docs
 */
class Version20130606150148 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE TABLE typo3_docs_domain_model_package (title VARCHAR(150) NOT NULL, abstract LONGTEXT NOT NULL, product VARCHAR(30) NOT NULL, locale VARCHAR(30) NOT NULL, type VARCHAR(30) NOT NULL, packagekey VARCHAR(100) NOT NULL, uri VARCHAR(255) NOT NULL, repository VARCHAR(255) NOT NULL, version VARCHAR(100) NOT NULL, repositorytype VARCHAR(30) NOT NULL, repositorytag VARCHAR(100) NOT NULL) ENGINE = InnoDB");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("DROP TABLE typo3_docs_domain_model_package");
	}
}

?>