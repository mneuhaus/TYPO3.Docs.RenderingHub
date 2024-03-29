<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140918235524 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		// this up() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentbuild DROP FOREIGN KEY FK_68DE43B8F143BFAD");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentbuild ADD CONSTRAINT FK_68DE43B8F143BFAD FOREIGN KEY (variant) REFERENCES typo3_docs_renderinghub_domain_model_documentvariant (persistence_object_identifier) ON DELETE CASCADE");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentsource DROP FOREIGN KEY FK_8BE43120F143BFAD");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentsource ADD CONSTRAINT FK_8BE43120F143BFAD FOREIGN KEY (variant) REFERENCES typo3_docs_renderinghub_domain_model_documentvariant (persistence_object_identifier) ON DELETE CASCADE");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentvariant DROP FOREIGN KEY FK_3F5D1ADD8698A76");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentvariant ADD CONSTRAINT FK_3F5D1ADD8698A76 FOREIGN KEY (document) REFERENCES typo3_docs_renderinghub_domain_model_document (persistence_object_identifier) ON DELETE CASCADE");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		// this down() migration is autogenerated, please modify it to your needs
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
		
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentbuild DROP FOREIGN KEY FK_68DE43B8F143BFAD");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentbuild ADD CONSTRAINT FK_68DE43B8F143BFAD FOREIGN KEY (variant) REFERENCES typo3_docs_renderinghub_domain_model_documentvariant (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentsource DROP FOREIGN KEY FK_8BE43120F143BFAD");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentsource ADD CONSTRAINT FK_8BE43120F143BFAD FOREIGN KEY (variant) REFERENCES typo3_docs_renderinghub_domain_model_documentvariant (persistence_object_identifier)");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentvariant DROP FOREIGN KEY FK_3F5D1ADD8698A76");
		$this->addSql("ALTER TABLE typo3_docs_renderinghub_domain_model_documentvariant ADD CONSTRAINT FK_3F5D1ADD8698A76 FOREIGN KEY (document) REFERENCES typo3_docs_renderinghub_domain_model_document (persistence_object_identifier)");
	}
}