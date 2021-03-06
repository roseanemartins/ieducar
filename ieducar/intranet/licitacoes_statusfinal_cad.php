<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Licita&ccedil;&otilde;es!" );
		$this->processoAp = "138";
	}
}

class indice extends clsCadastro
{
	var $cod_status;
	var $nm_final;

	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";
		
		if (@$_GET['cod'])
		{
			$this->fexcluir = true;
			$this->cod_status = @$_GET['cod'];
			$db = new clsBanco();
			$db->Consulta( "SELECT nm_final FROM compras_final_pregao WHERE cod_compras_final_pregao = '{$this->cod_status}'" );
			if ($db->ProximoRegistro())
			{
				list( $this->nm_final ) = $db->Tupla();
			}
			$retorno = "Editar";
		}

		$this->url_cancelar = "licitacoes_statusfinal_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod", $this->cod_status );
		$this->campoTexto( "nm_final", "Nome", $this->nm_final, 30, 30, true );
	}

	function Novo() 
	{
		$this->nm_final = $_POST["nm_final"];
		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM compras_final_pregao WHERE nm_final = '{$this->nm_final}'" );
		if( ! $db->Num_Linhas() )
		{
			$db->Consulta( "INSERT INTO compras_final_pregao (nm_final) VALUES ('{$this->nm_final}')" );
			echo "<script>document.location.href='licitacoes_statusfinal_lst.php';</script>";
			return true;
		}
		else 
		{
			$this->mensagem = "J� existe um status de finaliza��o com este nome";
		}
		return false;
	}

	function Editar() 
	{
		$this->nm_final = $_POST["nm_final"];
		$this->cod_status = $_POST["cod"];
		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM compras_final_pregao WHERE cod_compras_final_pregao = '{$_POST["cod"]}'" );
		if( $db->Num_Linhas() )
		{
			$db->Consulta( "UPDATE compras_final_pregao SET nm_final = '{$this->nm_final}' WHERE cod_compras_final_pregao = '{$this->cod_status}'" );
			header( "location: licitacoes_statusfinal_lst.php" );
			return true;
		}
		else 
		{
			$this->mensagem = "N�o foi possivel encontrar este status de finalizacao";
		}
		return false;
	}

	function Excluir()
	{
		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM compras_final_pregao WHERE cod_compras_final_pregao = '{$_POST["cod"]}'" );
		if( $db->Num_Linhas() )
		{
			$db->Consulta( "DELETE FROM compras_final_pregao WHERE cod_compras_final_pregao = '{$_POST["cod"]}'" );
			header( "location: licitacoes_statusfinal_lst.php" );
			return true;
		}
		return false;
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
