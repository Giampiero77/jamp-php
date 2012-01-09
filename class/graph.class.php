<?php
/**
* Class for managing the graphs
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class clsGraphics
{	
	var $graphic;
	var $graphics;
	var $path;
	var $graph;	
	var $refresh;

	/**
	* Construct
	*/
	public function __construct($graphic, $graphics)
	{
		global $system;	
		function_exists('gd_info') or ClsError::showError("GRAPH000");
		$this->path = $system->dir_real_jamp."/".$system->dir_plugin.'jpgraph/src/';
		require_once($this->path.'jpgraph.php');
		$this->graphic = $graphic;
		$this->graphics = $graphics;
		$this->refresh = true;
	}

	/**
	* Returns true if the data input is an integer
	*/
	private function isInteger($input)
	{
		return(ctype_digit(strval($input)));
	}

	/**
	* Generic function for the recovery and the cast of the data
	*/
	private function getValue($values, $sep)
	{
		$ar = array();
		if (isset($values) && ($values!="")) 
		{
 			$ar = explode($sep, $values);
			for ($y=0; $y<count($ar); $y++)
			{
				$value = trim($ar[$y]);
				if ($this->isInteger($value)) $ar[$y] = (integer)$value;
				else if (is_numeric($value)) $ar[$y] = (float)$value;
				else if ($value=="true") $ar[$y] = true;
				else if ($value=="false") $ar[$y] = false;
				else if (defined($value)) $ar[$y] = constant($value);
			}
		}
		return $ar;
	}

	/**
	* Return the values of the line (for Gantt)
	*/
	private function getGanttValue($name, $value, $pos, $i = -1)
	{
		$val = $this->getValue($this->graphic->getPropertyName($name, $i), ",");
		if (count($val)==0) $val[0] = "";
		if ($val[0]=="" && isset($value[$pos])) $val[0] = $value[$pos];
		return $val[0];
	}

	/** 
	* Generic function for setting methods
	*/
	private function setValue($obj, $name, $method, $i = -1, $sep = ",")
	{
		$ar = $this->getValue($this->graphic->getPropertyName($name, $i), $sep);
		$count = count($ar);
 		if ($count==1) $obj->$method($ar[0]);
		else if ($count==2) $obj->$method($ar[0], $ar[1]);
		else if ($count==3) $obj->$method($ar[0], $ar[1], $ar[2]);
		else if ($count==4) $obj->$method($ar[0], $ar[1], $ar[2], $ar[3]);
		else if ($count==5) $obj->$method($ar[0], $ar[1], $ar[2], $ar[3], $ar[4]);
		else if ($count==6) $obj->$method($ar[0], $ar[1], $ar[2], $ar[3], $ar[4], $ar[5]);
		else if ($count==7) $obj->$method($ar[0], $ar[1], $ar[2], $ar[3], $ar[4], $ar[5], $ar[6]);
		else if ($count==8) $obj->$method($ar[0], $ar[1], $ar[2], $ar[3], $ar[4], $ar[5], $ar[6], $ar[7]);
	}

	/**
	* Function for the setting of methods without parameters
	*/
	private function setBool($obj, $name, $method, $i = -1)
	{
		$bool = $this->graphic->getPropertyName($name, $i);
		if ($bool=="true") $obj->$method();
	}

	/** 
	* Generic function for the setting of the ways separator |
	*/
	private function setPipeValue($obj, $name, $method, $i = -1)
	{
		$ar = $this->getValue($this->graphic->getPropertyName($name, $i), "|");
		$count = count($ar);	
		if ($count==1 && $ar[0]!="") $obj->$method($ar[0]);
		else if ($count==2) $obj->$method($ar[0] | $ar[1]);
		else if ($count==3) $obj->$method($ar[0] | $ar[1] | $ar[2]);
		else if ($count==4) $obj->$method($ar[0] | $ar[1] | $ar[2] | $ar[3]);
		else if ($count==5) $obj->$method($ar[0] | $ar[1] | $ar[2] | $ar[3] | $ar[4]);
		else if ($count==6) $obj->$method($ar[0] | $ar[1] | $ar[2] | $ar[3] | $ar[4] | $ar[5]);
		else if ($count==7) $obj->$method($ar[0] | $ar[1] | $ar[2] | $ar[3] | $ar[4] | $ar[5] | $ar[6]);
		else if ($count==8) $obj->$method($ar[0] | $ar[1] | $ar[2] | $ar[3] | $ar[4] | $ar[5] | $ar[6] | $ar[7]);
	}

	/**
	* Sets the title
	*/
	private function setTitle($i, $obj)
	{
		$title = $this->graphic->getPropertyName("title", $i);
		if (isset($title)) 
		{
			$obj->Set($this->graphic->getPropertyName("title", $i));
			$this->setValue($obj, "font", "SetFont", $i);
			$this->setValue($obj, "angle", "SetAngle", $i);
			$this->setValue($obj, "margin", "SetMargin", $i);
		}
	}

	/**
	* Sets the label
	*/
	private function setLabel($i, $obj)
	{
		$label = $this->graphic->getPropertyName("label", $i);
		$dsobj = $this->graphic->getPropertyName("dsobj", $i);
		$dsitem = $this->graphic->getPropertyName("dsitem", $i);
		if (!isset($label)) 
		{
			if (isset($dsobj) && isset($dsitem)) 
			{
				global $xml;	
				$ds = $xml->getObjById($dsobj);
				if (isset($ds))
				{
					$ds->ds->dsMoveRow(0);
					while($row = $ds->ds->dsGetRow()) $label[] = $row->$dsitem;
					if (is_array($label)) $label = implode(",",$label);
				}
				else $this->refresh = false;
			}
		}
		if (isset($label)) 
		{
			$obj->SetTickLabels(explode(",", $label));
			$this->setValue($obj, "labelformatstring", "SetLabelFormatString", $i);
			$this->setValue($obj, "angle", "SetLabelAngle", $i);
			$this->setBool($obj, "hide", "HideLabels", $i);
			$this->setValue($obj, "margin", "SetLabelMargin", $i);
			$this->setValue($obj, "align", "SetLabelAlign", $i);
		}
	}

	/**
	* Gets data
	*/
	private function getData($i, $name = "data", $field = "dsitem")
	{
		$data = $this->graphic->getPropertyName($name, $i);
		$dsobj = $this->graphic->getPropertyName("dsobj", $i);
		$dsitem = $this->graphic->getPropertyName($field, $i);
		$idwhere = $dsobj."where";
		if (!isset($data)) 
		{
			if (isset($dsobj) && isset($dsitem)) 
			{
				global $xml;	
				$ds = $xml->getObjById($dsobj);
				if (isset($ds))
				{
					$ds->ds->dsMoveRow(0);
					while($row = $ds->ds->dsGetRow()) $data[] = $row->$dsitem;
					if (is_array($data)) $data = implode(",",$data);
				}
				else $this->refresh = false;
			}
		}
		return $data;
	}

	/**
	* Create the graph
	*/
	public function Paint($file = false)
	{
		global $event;
		$graphic = $this->graphic;
		$objtype = $graphic->getPropertyName("objtype");
		$width = $graphic->getPropertyName("width");
		$height = $graphic->getPropertyName("height");
		$graph = null; 
		for ($i=0; $i<count($objtype);$i++) 
		{
			$numplot = 0;
			switch ($objtype[$i])
			{
				case "bar":
					require_once($this->path."jpgraph_bar.php");
					if (!($graph instanceof Graph)) $graph = new Graph($width, $height); 
					$data = $this->getData($i);
					if (isset($barplot)) $numplot = count($barplot); 
					$barplot[$numplot] = new BarPlot(explode(",",$data));
					$this->setValue($barplot[$numplot], "legend", "SetLegend", $i);
					$this->setValue($barplot[$numplot], "color", "SetColor", $i);
					$this->setValue($barplot[$numplot], "fillcolor", "SetFillColor", $i);
					$this->setValue($barplot[$numplot]->value, "font", "SetFont", $i);
					$this->setValue($barplot[$numplot], "shadow", "SetShadow", $i);
					$this->setValue($barplot[$numplot], "fillgradient", "SetFillGradient", $i);
					$this->setValue($barplot[$numplot]->value, "format", "SetFormat", $i);
					$barplot[$numplot]->value->Show();
				break;	
				case "line":
					require_once($this->path."jpgraph_line.php");
					if (!($graph instanceof Graph)) $graph = new Graph($width, $height); 
					$data = $this->getData($i);
					if (isset($lineplot)) $numplot = count($lineplot); 
					$lineplot[$numplot] = new LinePlot(explode(",",$data));
					$this->setValue($lineplot[$numplot], "legend", "SetLegend", $i);
					$this->setValue($lineplot[$numplot], "color", "SetColor", $i);
					$this->setValue($lineplot[$numplot], "fillcolor", "SetFillColor", $i);
					$this->setValue($lineplot[$numplot], "style", "SetStyle", $i);
   				$this->setValue($lineplot[$numplot]->mark, "marktype", "SetType", $i);
					$this->setValue($lineplot[$numplot]->mark, "markcolor", "SetColor", $i);
					$this->setValue($lineplot[$numplot]->mark, "markfillcolor", "SetFillColor", $i);
					$this->setValue($lineplot[$numplot], "linewidth", "SetWeight", $i);
					$this->setValue($lineplot[$numplot], "center", "SetCenter", $i);
					$this->setValue($lineplot[$numplot]->value, "font", "SetFont", $i);
					$graph->Add($lineplot[$numplot]);
				break;
				case "stock":
					require_once($this->path."jpgraph_stock.php");
					if (!($graph instanceof Graph)) $graph = new Graph($width, $height); 
					$data = $this->getData($i);
					if (isset($stockplot)) $numplot = count($stockplot); 
					$stockplot[$numplot] = new StockPlot(explode(",",$data));
					$this->setValue($stockplot[$numplot], "legend", "SetLegend", $i);
					$this->setValue($stockplot[$numplot], "color", "SetColor", $i);
					$this->setValue($stockplot[$numplot], "linewidth", "SetWidth", $i);
					$this->setValue($stockplot[$numplot]->value, "font", "SetFont", $i);
					$this->setValue($stockplot[$numplot]->value, "format", "SetFormat", $i);
					$graph->Add($stockplot[$numplot]);
				break;
				case "spline":
					require_once($this->path."jpgraph_line.php");
					require_once($this->path."jpgraph_regstat.php");
					if (!($graph instanceof Graph)) $graph = new Graph($width, $height); 
					$xdata = $this->getData($i, "xdata");
					$ydata = $this->getData($i, "ydata");
					if (isset($spline)) $numplot = count($spline); 
					$spline[$numplot] = new Spline(explode(",",$xdata), explode(",",$ydata));
					list($newx,$newy) = $spline[$numplot]->Get(50);
					$this->setValue($spline[$numplot], "legend", "SetLegend", $i);
					$this->setValue($spline[$numplot], "linewidth", "SetWidth", $i);
					$this->setValue($spline[$numplot], "center", "SetCenter", $i);
					if (isset($lineplot)) $numplot = count($lineplot); 
					$lineplot[$numplot] = new LinePlot($newy,$newx);
					$this->setValue($lineplot[$numplot]->value, "color", "SetColor", $i);
					$this->setValue($lineplot[$numplot]->value, "font", "SetFont", $i);
					$this->setValue($lineplot[$numplot]->value, "format", "SetFormat", $i);
					$graph->Add($lineplot[$numplot]);
				break;
				case "scatter":
					require_once($this->path."jpgraph_scatter.php");
					if (!($graph instanceof Graph)) $graph = new Graph($width, $height); 
					$xdata = $this->getData($i, "xdata");
					$ydata = $this->getData($i, "ydata");
					if (isset($scatter)) $numplot = count($scatter); 
					$scatter[$numplot] = new ScatterPlot(explode(",",$ydata), explode(",",$xdata));
					$this->setValue($scatter[$numplot], "legend", "SetLegend", $i);
					$this->setValue($scatter[$numplot], "color", "SetColor", $i);
					$this->setValue($scatter[$numplot], "linewidth", "SetWidth", $i);
					$this->setValue($scatter[$numplot], "center", "SetCenter", $i);
					$this->setValue($scatter[$numplot]->value, "font", "SetFont", $i);
					$this->setValue($scatter[$numplot]->value, "format", "SetFormat", $i);
					$graph->Add($scatter[$numplot]);
				break;
				case "polar":
					require_once($this->path."jpgraph_polar.php");
					if (!($graph instanceof PolarGraph)) $graph = new PolarGraph($width, $height); 
					$graph->SetScale($graphic->getPropertyName("scale"));	
					$data = $this->getData($i);
					$xaxis = $graphic->getPropertyName("xaxis");
					$yaxis = $graphic->getPropertyName("yaxis");
					$graph->axis->ShowGrid($xaxis, $yaxis);
					if (isset($polar)) $numplot = count($polar);
					$polar[$numplot] = new PolarPlot(explode(",",$data));
					$this->setValue($polar[$numplot], "legend", "SetLegend", $i);
					$this->setValue($polar[$numplot], "color", "SetColor", $i);
					$this->setValue($polar[$numplot], "linewidth", "SetFont", $i);
					$this->setValue($polar[$numplot], "center", "SetCenter", $i);
					$this->setValue($polar[$numplot], "fillcolor", "setFillColor", $i);
					$this->setValue($polar[$numplot]->mark, "type", "SetType", $i);
					$graph->Add($polar[$numplot]);
				break;
				case "radar":
					require_once($this->path."jpgraph_radar.php");
					if (!($graph instanceof RadarGraph)) $graph = new RadarGraph($width, $height, "auto"); 
					$data = $this->getData($i);
					if (isset($polar)) $numplot = count($polar);
					$radar[$numplot] = new RadarPlot(explode(",",$data));
					$this->setValue($radar[$numplot], "legend", "SetLegend", $i);
					$this->setValue($radar[$numplot], "color", "SetColor", $i);
					$this->setValue($radar[$numplot], "linewidth", "SetWidth", $i);
					$this->setValue($radar[$numplot], "center", "SetCenter", $i);
					$this->setValue($radar[$numplot], "fillcolor", "SetFillColor", $i);
					$graph->Add($radar[$numplot]);
				break;
				case "pie":
					require_once($this->path."jpgraph_pie.php");
					require_once($this->path."jpgraph_pie3d.php");
					if (!($graph instanceof PieGraph)) $graph = new PieGraph($width, $height, "auto"); 
					$data = $this->getData($i);
					if ($data =="") $data = "0,100"; 
					if (isset($pie)) $numplot = count($pie);
					$pie3d = $graphic->getPropertyName("pie3d", $i);
					if ($pie3d=="true") $pie[$numplot] = new PiePlot3d(explode(",",$data)); 
					else 
					{
						$pie[$numplot] = new PiePlot(explode(",",$data));
						$guideline = $graphic->getPropertyName("guideline", $i);
						if ($guideline=="true")
						{
							$pie[$numplot]->SetGuideLines();
							$this->setValue($pie[$numplot], "guidelinesadjust", "SetGuideLinesAdjust", $i);
							$this->setValue($pie[$numplot], "labeltype", "SetLabelType", $i);
							$pie[$numplot]->value->Show();
						}
					}		

					$legends = $this->getData($i, "legend", "dslegend");
					$pie[$numplot]->SetLegends(explode(",",$legends));
					$this->setValue($pie[$numplot], "color", "SetColor", $i);
					$this->setValue($pie[$numplot], "center", "SetCenter", $i);
					$this->setValue($pie[$numplot], "fillcolor", "SetFillColor", $i);
					$this->setValue($pie[$numplot], "size", "SetSize", $i);
					$this->setValue($pie[$numplot], "angle", "SetAngle", $i);
					$this->setValue($pie[$numplot]->value, "font", "SetFont", $i);
					$this->setValue($pie[$numplot]->value, "format", "SetFormat", $i);
					$explode = $graphic->getPropertyName("explode", $i);
					if (isset($explode)) 
					{
						if ($explode=="true") 
						{
							$ar = explode(",", $data);
							for ($k=0; $k<count($ar); $k++) $array[$k] = 15;
						}	
						else 
						{
							$explode = $this->getData($i, "explode");
							$array[$k] = explode(",", $explode);
						}						
						$pie[$numplot]->Explode($array);
					}	
				  
					$graph->Add($pie[$numplot]);
				break;
				case "gantt":
					require_once($this->path."jpgraph_gantt.php");
					$graph = new GanttGraph($width, $height, "auto");
					$count = 0;
					if (isset($gantt)) $count = count($gantt);
 					$data = $this->getData($i);
  					$col = $this->getValue($data, ",");
					$type = $this->getGanttValue("type", $col, 0, $i);
					$label = $this->getGanttValue("label", $col, 1, $i);
					$start = $this->getGanttValue("start", $col, 2, $i);
					$end = $this->getGanttValue("end", $col, 3, $i);
					$bo = $this->getGanttValue("bo", $col, 4, $i);
					$link = $this->getGanttValue("link", $col, 5, $i);
					$alt = $this->getGanttValue("alt", $col, 6, $i);
 					$gantt[] = array($count, $type, $label, $start, $end, $bo, $link, $alt);
				break;
				case "multigraph": // Grafico multiplo
					require_once($this->path."jpgraph_mgraph.php");
 					if (!($graph instanceof MGraph)) $graph = new MGraph();
					$idgraphic = $this->graphic->getPropertyName("idgraphic", $i); 
					$left = $this->graphic->getPropertyName("left", $i); 
					$top = $this->graphic->getPropertyName("top", $i); 
					$graph->Add($this->graphics[$idgraphic], $left, $top);
				break;
			}	
		}	
		$groupbar = false;
		if (isset($graph))
		{
			$this->setValue($graph, "scale", "SetScale");
			if (isset($gantt)) $graph->CreateSimple($gantt, array(), array());
			for ($i=0; $i<count($objtype);$i++) 
			{
				switch ($objtype[$i])
				{
					case "plot":
							$this->setValue($graph, "perspective", "Set3DPerspective", $i);
							$groupbar = $this->graphic->getPropertyName("groupbar", $i);
							$this->setValue($graph, "shadow", "SetShadow", $i);	
							$this->setValue($graph, "frame", "SetFrame", $i);
							$this->setValue($graph, "center", "SetCenter", $i);
							$this->setValue($graph->img, "antialiasing", "SetAntiAliasing", $i);
							$this->setValue($graph, "angle", "SetAngle", $i);	
							$this->setValue($graph, "bgimage", "SetBackgroundImage", $i);	
							$this->setBool($graph, "box", "SetBox", $i);
							$this->setValue($graph->img, "margin", "SetMargin", $i);
							$this->setValue($graph, "color", "SetMarginColor", $i);
							if (isset($barplot))
							{
								if ($groupbar=="true") $gbplot = new GroupBarPlot($barplot);
								else $gbplot = new AccBarPlot($barplot);
								$this->setValue($gbplot, "plotwidth", "SetWidth", $i);	
								$graph->Add($gbplot);
							}
						break;
					case "title":
							$this->setTitle($i, $graph->title);
							$titles = $this->graphic->getPropertyName("titles", $i);
							if (isset($titles)) $graph->SetTitles(explode(",",$titles));
							$this->setPipeValue($graph, "header", "ShowHeaders", $i); // gannt
						break;
					case "subtitle":
							$this->setTitle($i, $graph->subtitle);
						break;
					case "tabtitle":
							$this->setValue($graph->tabtitle, "title", "Set", $i);
							$this->setValue($graph->tabtitle, "font", "SetFont", $i);
							$this->setValue($graph->tabtitle, "color", "SetColor", $i);
							$this->setValue($graph->tabtitle, "tabwidth", "SetWidth", $i);
						break;
					case "xaxis":
							$this->setTitle($i, $graph->xaxis->title);
							$this->setLabel($i, $graph->xaxis);
							$this->setValue($graph->xaxis, "pos", "SetPos", $i);
							$this->setValue($graph->xaxis, "tickpositions", "SetTickPositions", $i);
							$this->setValue($graph->xaxis, "tickside", "SetTickSide", $i);
						break;
					case "yaxis":
							$this->setTitle($i, $graph->yaxis->title);
							$this->setLabel($i, $graph->yaxis);
							$this->setValue($graph->yaxis, "pos", "SetPos", $i);
							$this->setValue($graph->yaxis, "tickpositions", "SetTickPositions", $i);
							$this->setValue($graph->yaxis, "tickside", "SetTickSide", $i);
						break;
					case "axis":
							$this->setValue($graph->axis, "font", "SetFont", $i);
							$this->setValue($graph->axis, "weight", "SetWeight", $i);
						break;
					case "xgrid":
							$this->setValue($graph->xgrid, "color", "SetColor", $i);
							$this->setValue($graph->xgrid, "fill", "SetFill", $i);
							$this->setValue($graph->xgrid, "linestyle", "SetLineStyle", $i);
							$graph->xgrid->Show();
						break;
					case "ygrid":
							$this->setValue($graph->ygrid, "color", "SetColor", $i);
							$this->setValue($graph->ygrid, "fill", "SetFill", $i);
							$this->setValue($graph->ygrid, "linestyle", "SetLineStyle", $i);
							$graph->ygrid->Show();
						break;
					case "legend":
							$this->setValue($graph->legend, "pos", "Pos", $i);
							$this->setValue($graph->legend, "abspos", "SetAbsPos", $i);
							$this->setValue($graph->legend, "shadow", "SetShadow", $i);
							$this->setValue($graph->legend, "color", "SetColor", $i);
							$this->setValue($graph->legend, "fillcolor", "SetFillColor", $i);
							$this->setValue($graph->legend, "font", "SetFont", $i);
							$this->setValue($graph->legend, "weight", "SetLineWeight", $i);
						break;
					case "scale": // Grafico tipo gant
							$this->setValue($graph->scale->week, "weekstyle", "SetStyle", $i);
							$this->setValue($graph->scale->week, "weekfont", "SetFont", $i);
							$this->setValue($graph->scale->week, "weekbgcolor", "SetBackgroundColor", $i);
							$this->setValue($graph->scale->month, "monthstyle", "SetStyle", $i);
							$this->setValue($graph->scale->month, "monthfont", "SetFont", $i);
							$this->setValue($graph->scale->month, "monthbgcolor", "SetBackgroundColor", $i);
						break;
				}	
			}	
		}	
		$this->graph = $graph;
		$return = $event->callEvent("before_plot", $this);
		if (is_null($return) || ($return==true))
		{
			$gdImgHandler = $this->graph->Stroke(_IMG_HANDLER);
			$this->graph->Stroke($file);
		}
		$event->callEvent("after_plot", $this);
		return $this->graph;
	}	
}
?> 