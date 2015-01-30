<?php
/**
 * MapSession Module
 *
 * @project     PHP Wrapper Class
 * @revision    $Id: map_session.php,v 1.42 2002/07/07 17:28:30 pspencer Exp $
 * @purpose     This file contains classes related to managing map sessions.
 *              There are two session types that can be instanciated, a read-
 *              only version and a read/write version.  Of the two, only the
 *              latter needs write access to the physical disk.
 * @author      William A. Bronsema, C.E.T. (bronsema@dmsolutions.ca)
 * @copyright
 * <b>Copyright (c) 2001, DM Solutions Group Inc.</b>
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

/**
 * This class is the basic wrapper for the map file and provides basic file
 * i/o and state management.
 * It is assummed that the phpmapscript module is loaded prior to
 * instantiaiting this class.
 *
 * @author William A. Bronsema, C.E.T. (bronsema@dmsolutions.ca)
 *
 */
class MapSession
{
    /**
     * The current session map file object (private).
     */
    var $oMap;

    /**
     * The current session map file (private).
     */
    var $szMapFile;

    /**
     * The temp directory to write the sessions to (private).
     */
    var $szTempDir;

    /**
     * Array of max extents (private).
     */
    var $adMaxExtents;

    /**
     *  Construct a new MapSession instance and initialize it.
     */
    function MapSession()
    {
        // initialize variables
        $this->oMap = null;
        $this->szMapFile = "";
        $this->szTempDir = "";
        $this->adMaxExtents = array();

    // end constructor
    }

    /**
     * This function sets temp directory for the MapSession.
     *
     * @param szTempDir string - Path to the tenp directory.
     */
    function setTempDir($szTempDir)
    {
        $this->szTempDir = $szTempDir;
    // end setTempDir function
    }

    /**
     * This function sets the optional maximum extents to be used to limit
     * zooming and panning.
     *
     * @param dMinX double - The min X extent to use.
     * @param dMinY double - The min Y extent to use.
     * @param dMaxX double - The max X extent to use.
     * @param dMaxY double - The max Y extent to use.
     * @return boolean - True if successful, False if not.
     **/
    function setMaxExtents($dMinX, $dMinY, $dMaxX, $dMaxY)
    {
        
        // check if the parameters are valid numbers and min is less than max
        if ( !( is_numeric($dMinX) && is_numeric($dMinY) &&
                is_numeric($dMaxX) && is_numeric($dMaxY) &&
                $dMinX < $dMaxX && $dMinY < $dMaxY))
        {
            return false;
        }

        // set the maxextent array
        $this->adMaxExtents["minX"] = $dMinX;
        $this->adMaxExtents["minY"] = $dMinY;
        $this->adMaxExtents["maxX"] = $dMaxX;
        $this->adMaxExtents["maxY"] = $dMaxY;

    // end setMaxExtents function
    }

    /**
     * This function returns an associative array of extents if they were set
     * or false if they were not.  The indexes are as follows:
     *          array["minX"]
     *          array["minY"]
     *          array["maxX"]
     *          array["maxY"]
     *
     * @return mixed - Associative array of extents if set or false if not.
     **/
    function getMaxExtents()
    {
        
        // count the array items and if greater than 0 return
        if ( count( $this->adMaxExtents ) > 0 )
        {
            // return the array because extents are set
            $ReturnValue = $this->adMaxExtents;
        }
        else
        {
           $ReturnValue = false;
        }

        // return
        return $ReturnValue;

    // end getMaxExtents function
    }

    /**
     * This function opens a map file and sest oMap to a valid state.
     *
     * @param szMapFile string - Path and filename of the map to open.
     * @param szMapFilePath string - Path of the map to open. This is set
     *                               when a different path is used.
     *
     * @return boolean - True if successful, false if not.
     */
    function readMapFile($szMapFile, $szMapFilePath="")
    {
        
        // check to see if the map file exists
        if ( !file_exists($szMapFile) )
        {
          // return failure
            return false;
        }

        if ($szMapFilePath != "" && is_dir($szMapFilePath))
        {
            $this->oMap = ms_newMapObj($szMapFile, $szMapFilePath);
        }
        else
        {
            // set the map object to be the phpmapscript map object
            $this->oMap = ms_newMapObj($szMapFile);
        }

        // check for errors
        if ( !isset($this->oMap) )
        {
            return false;
        }

        // set the extents to validate them
        $this->oMap->setextent($this->oMap->extent->minx,
                               $this->oMap->extent->miny,
                               $this->oMap->extent->maxx,
                               $this->oMap->extent->maxy);

        // record the map path and filename;
        $this->szMapFile = $szMapFile;

        // return success
        return true;

    // end readMapFile function
    }

    /**
     * This function saves the current map object to the mapfile.
     *
     * @param szMapFile string - Path and filename of the map to write to.
     *
     * @return boolean - True if successful, false if not.
     */
    function writeMapFile($szMapFile)
    {
        $bResult = true;
      
        // check to see that there is a valid map file open
        if ( !isset($this->oMap) )
        {
            return false;
        }

        if ( file_exists( $szMapFile ) && !is_writable( $szMapFile ) )
        {
            $bResult = false;
        }
        else
        {
            // save the map object
            $this->oMap->save( $szMapFile );
        }

        // return success
        return $bResult;

    // end writeMapFile function
    }

    /**
     * This function returns a pointer to the current open map object.
     *
     * @return object - A pointer to the current open map object or null.
     */
    function getMapObj()
    {

        // return a pointer to the map object
        return $this->oMap;

    // end getMapObj function
    }

    /**
     * This function processes the template file as set in web object of the
     * current session's map object.  It returns the processed file as string.
     *
     * @param aszTag array - Optional associative array of user defined tags
     *              and their corresponding values to be processed.
     *              i.e. $aszTag["my_tag"] = "My tag's value". In this example
     *              all the tags [my_tag] will be replaced with the string "My
     *              tag's value".
     * @param nGenerateImages integer - This optional flag indicates how
     *              special tags are to be processed.  The special tags: [img],
     *              [scalebar], [ref], [legend] can be replaced with the
     *              appropriate URL if this flag is set to MS_TRUE.
     * @return mixed - A string representation(typically HTML) of the template
     *              file with all tags processed or false if failed.
     **/
    function processTemplate( $aszTag = array(), $nGenerateImages = MS_FALSE )
    {


        // check to see that there is a valid map file open
        if ( !isset($this->oMap) )
        {
            return false;
        }

        // check to see if the the tag array is a valid array
        if ( !is_array( $aszTag ) )
        {
            return false;
        }

        // call mapscript's proceestemplate
        return $this->oMap->processtemplate( $aszTag, $nGenerateImages );

    // end processTemplate function
    }

   /**
     * This function processes the query template file as set in each layer's
     * class object of the current session's map object.  It returns the
     * processed file as string.
     *
     * @param aszTag array - Optional associative array of user defined tags
     *              and their corresponding values to be processed.
     *              i.e. $aszTag["my_tag"] = "My tag's value". In this example
     *              all the tags [my_tag] will be replaced with the string "My
     *              tag's value".
     * @return mixed - A string representation(typically HTML) of the template
     *              file with all tags processed or false if failed.
     **/
    function processQueryTemplate( $aszTag = array() )
    {
        
        // check to see that there is a valid map file open
        if ( !isset($this->oMap) )
        {
            return false;
        }

        // check to see if the the tag array is a valid array
        if ( !is_array( $aszTag ) )
        {
            return false;
        }

        // call mapscript's proceesquerytemplate
        return $this->oMap->processquerytemplate( $aszTag );

    // end processQueryTemplate function
    }

    /**
     * This function processes the legend template file as set in the legend
     * object of the current session's map object.  It returns the processed
     * file as string.
     *
     * @param aszTag array - Optional associative array of user defined tags
     *              and their corresponding values to be processed.
     *              i.e. $aszTag["my_tag"] = "My tag's value". In this example
     *              all the tags [my_tag] will be replaced with the string "My
     *              tag's value".
     * @return mixed - A string representation(typically HTML) of the template
     *              file with all tags processed or false if failed.
     **/
    function processLegendTemplate( $aszTag = array() )
    {
        // check to see that there is a valid map file open
        if ( !isset($this->oMap) )
        {
            // return failure
            return false;
        }

        // check to see if the the tag array is a valid array
        if ( !is_array( $aszTag ) )
        {
            // return failure
            return false;
        }

        // process the template
        $szResult = $this->oMap->processlegendtemplate( $aszTag );
        return $szResult;

    // end processLegendTemplate function
    }

// end MapSession class
}

/**
 * This class extends the base MapSession Class to allow a save and restore
 * state option.  This class is the read only version meaning that the save &
 * restore sessions will NOT wrote to the physical disk.  There is also a
 * read/write version of this class called MapSession_RW.
 * It is assummed that the phpmapscript module is loaded prior to
 * instantiaiting this class.
 *
 * @author William A. Bronsema, C.E.T. (bronsema@dmsolutions.ca)
 *
 */
class MapSession_R extends MapSession
{
    /**
     *  Construct a new MapSession instance and initialize it.
     */
    function MapSession_R()
    {
        // call the constructor for the map session base class
        $this->MapSession();
    // end constructor
    }

    /**
     * This function saves the current state of the map.
     *
     * @return string - A unique state ID or "" if failed.
     */
    function saveState()
    {
        // check to see that there is a valid map file open
        if ( !isset($this->oMap) )
        {
            // return failure
            return "";
        }

        // build the state id from the bbox, projection, and list of layers
        $szStateID = $this->buildStateID( $this->oMap );
        // return ID
        return $szStateID;

    // end the saveState function
    }

    /**
     * This function restores the state of a map file by applying the value
     * of the State ID to the map file.  If no map fil eis given then the
     * State ID will be applied to the current map.  If the state id is not
     * given but the map file is then the map file will be opened.
     *
     * @param szStateID - Optional unique state ID.
     * @param szMapFile - Optional mapfile to restore state to.
     * @param szMapFilePath - Optional mapfile path to restore state to.
     * @param bRestoreLayerState - Optionally turn on restoration of layer
     *        state from the state key.
     *
     * @return boolean - True if successful, false if not.
     */
    function restoreState( $szStateID = "", $szMapFile = "",$szMapFilePath = "",$bRestoreLayerStatus = true)
    {
        // check if map file was supplied
        if ( $szMapFile != "" )
        {
            // open the requested map file
            if ( !$this->readMapFile($szMapFile, $szMapFilePath) )
                return false;
        }

        // check for a current map object
        if ( $this->oMap == "" )
        {
            return "false";

        }

        // if no state given then done
        if ( $szStateID == "" )
        {
            return true;
        }

        // process the ID
        $aszElements = explode( "|", $szStateID);

        // process map size
        $aszElement = explode( "=", $aszElements[2] );

        // check if mapsize
        if ( $aszElement[0] != "MAPSIZE" )
        {
            return "false";

        }

        // set the mapsize if not empty
        if ( $aszElement[1] != "" )
        {
            // get the mapsize items
            $anMapsize = explode( "," , $aszElement[1] );
            $this->oMap->set( "width", $anMapsize[0]);
            $this->oMap->set( "height", $anMapsize[1]);

        }

        // process the BBOX
        $aszElement = explode( "=", $aszElements[0] );

        // check if BBOX
        if ( $aszElement[0] != "BBOX" )
        {
            return "false";

        }

        // separate out the extents
        $anExtents = explode(",",$aszElement[1]);

        // set the extents
        $this->oMap->setextent($anExtents[0],$anExtents[1],
                               $anExtents[2],$anExtents[3]);

        // process projection
        $aszElement = explode( "=", $aszElements[1] );

        // check if projection
        if ( $aszElement[0] != "SRS" )
        {
            // return failure
            return "false";

        }

        // set the projection if not empty
        if ( $aszElement[1] != "" )
        {
            // srs can contain "="
            $szTmpProj = substr($aszElements[1],4);
           // $this->oMap->setprojection($szTmpProj);
            //$this->oMap->setprojection($aszElement[1]);
        }

        // process the active layers
        $aszElement = explode( "=", $aszElements[3] );

        // check if projection
        if ( $aszElement[0] != "LAYERS" )
        {
            // return failure
            return "false";

        }

      if ($bRestoreLayerStatus)
     {

            // split the string into an array
            if ( $aszElement[1] != "" )
            {
                // split
                $anLayers = split(",",$aszElement[1] );
                sort($anLayers);
                reset($anLayers);
            }
            else
            {
                // otherwise default
                $anLayers = array();
            }

            // initialize the array counter
            $j = 0;
// print_r($anLayers);
            // loop through all the layers and set their values
            for ($i=0;$i<$this->oMap->numlayers;$i++)
            {
                // get layer object
                $oLayer = $this->oMap->getlayer($i);

                // check if the current layer matches the "ON" array
                if ( $i == $anLayers[$j] )
                {
                    // turn the layer on
                    $oLayer->set("status",MS_ON);

                    // get next item in the array
                    $j++;
                }
                else
                {
                  									 
                    // turn the layer on
                    $oLayer->set("status",MS_OFF);
                   // print_r($oLayer);

                }
            }
        }

        // return success
        return true;

    // end the restoreState function
    }

    /**
     * This function reads the given map object and builds a string to
     * to represent the state of the file in terms of:
     *  1) extents - key is "BBOX", values are space de-limited.
     *  2) projection - key is "SRS".
     *  3) map size - key is "MAPSIZE", values are comma de-limited.
     *  4) active layers - key is "LAYERS", values are comma de-limited.
     *
     * The three sections of the string will be separated by a "|".  Each
     * individual section will be in the following format:
     *      key=value (i.e. BBOX=0 0 100 100)
     *
     * @param oMap - Map file object to read.
     *
     * @return string - The state ID representing the current state if the
     *                  give map file.
     */
    function buildStateID( $oMap )
    {
        // initialize the return string
        $szReturn = "";

        // get the current extents
        $szReturn = "BBOX=".$oMap->extent->minx.",".$oMap->extent->miny.",".
                            $oMap->extent->maxx.",".$oMap->extent->maxy;

        // get the current map projection
        $szReturn .= "|SRS=".$oMap->getProjection();

        // get the current map size
        $szReturn .= "|MAPSIZE=".$oMap->width.",".$oMap->height;

        // build the list of active layers
        $szReturn .= "|LAYERS=";

        // get the drawing order
        $anDrawingOrder = $oMap->getlayersdrawingorder();
					//echo $oMap->numlayers;
        // loop to build list
        foreach ( $anDrawingOrder as $nIndex )
        {
            // create the layer object
            $oLayer = $oMap->getlayer( $nIndex );

            // check it's status and add if on
            if ( $oLayer->status == MS_ON || $oLayer->status == MS_DEFAULT )
            {
                $szReturn .= $oLayer->index.",";
            }
        }

        // remove the trailing ","
        $szReturn = substr($szReturn,0,strlen($szReturn)-1);

        // return the string
        return $szReturn;

    }

// end MapSession_R class
}

/**
 * This class extends the base MapSession Class to allow a save and restore
 * state option.  This class is the read-write version meaning that the save &
 * restore sessions will write/read to the physical disk.  There is also a read-
 * only version of this class called MapSession_R
 * It is assummed that the phpmapscript module is loaded prior to
 * instantiaiting this class.
 *
 * @author William A. Bronsema, C.E.T. (bronsema@dmsolutions.ca)
 *
 */
class MapSession_RW extends MapSession
{
    /**
     *  Construct a new MapSession instance and initialize it.
     */
    function MapSession_RW()
    {
        // call the constructor for the map session base class
        $this->MapSession();
    }

    /**
     * This function saves the current state of the map.
     * It requires that the temp directory been set.
     *
     * @return string - A unique state ID or "" if failed.
     */
    function saveState()
    {
       // check to see if the temp directory has been set
        if ( !isset($this->szTempDir) )
        {
            return "";
        }

        // check to see if the temp directory is a valid directory
        if ( !is_dir($this->szTempDir) )
        {
            return "";
        }

        // get the unique state identifier
        $szStateID = time()."-".rand(1000,9999);
        // write the PrevStateID to the map object
        //$this->oMap->setmetadata( "PrevStateID", $this->szPrevStateID );

        // write the map to a temp directory
        if ( !$this->writeMapFile( $this->szTempDir.$szStateID.".map" ) )
            return "";

        // update the previous state id
        //$this->szPrevStateID = $szStateID;

        // return ID
        return $szStateID;

    // end the saveState function
    }

    /**
     * This function restores the state of a map file by opening the map file
     * in the temp directory corresponding to the supplied ID.  The restored
     * state becomes the current map object.
     * It requires that the temp directory been set.
     *
     * @param szStateID - Optional unique state ID.
     * @param szMapFile - Optional mapfile to restore state to.
     * @param szMapFilePath - Optional mapfile path to restore state to.
     *
     * @return boolean - True if successful, false if not.
     */
    function restoreState( $szStateID = "", $szMapFile = "",
                                                        $szMapFilePath = "" )
    {

        // check if map file was supplied
        if ( $szMapFile == "" )
            $szMapFile = $this->szMapFile;

        // check for a valid mapfile
        if ( $szMapFile == "" )
        {
            return "false";

        }

        // if no state given then just open the map
        if ( $szStateID == "" )
        {
            // check for success
            if ( !$this->readMapFile($szMapFile, $szMapFilePath) )
                return false;
          
                return true;
        }

        // check to see if the temp directory has been set
        if ( !isset($this->szTempDir) )
        {
            return "false";
        }

        // check to see if the temp directory is a valid directory
        if ( !is_dir($this->szTempDir) )
        {
            return "false";
        }

        // build the map names
        $szSessionMap = $this->szTempDir.$szStateID.".map";

        // check for a valid session map file
        if( !file_exists($szSessionMap) )
        {
            return "false";
        }

        // attempt to open the map
        if ( !$this->readMapFile( $szSessionMap, $szMapFilePath ) )
            return false;

        // return success
        return true;

    // end the restoreState function
    }

// end MapSession_RW class
}

?>
