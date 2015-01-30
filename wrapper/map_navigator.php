<?php
/**
 * MapNavigator Module
 * 
 * @project     PHP Wrapper Class
 * @revision    $Id: map_navigator.php,v 1.13 2002/05/10 18:25:20 pspencer Exp $
 * @purpose     This file contains classes related to managing map navigation. 
 * @author      William A. Bronsema, C.E.T. (bronsema@dmsolutions.ca)
* @author      FX Gamoy (fx.gamoy@geomatika.fr)
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
 */
 

// define the pan directions
define( "NORTH"     , 0 );
define( "NORTH_EAST", 1 );
define( "EAST"      , 2 );
define( "SOUTH_EAST", 3 );
define( "SOUTH"     , 4 );
define( "SOUTH_WEST", 5 );
define( "WEST"      , 6 );
define( "NORTH_WEST", 7 );

/**
 * This class encapsulates the manipulation of extents of the map file by 
 * providing common navigation functions that hide the complexity.
 * It is assummed that the phpmapscript module is loaded prior to 
 * instantiaiting this class.
 *
 * @author William A. Bronsema, C.E.T. (bronsema@dmsolutions.ca)
 *
 */
class MapNavigator
{       
    /**
     * The current session object (private).
     */
    var $oSession;
  
    /**
     * Construct a new MapNavigator instance and initialize it.
     *
     * @param oSession object - MapSession class object.
     */
    function MapNavigator( &$oSession )
    {
        // initialize variables
        $this->oSession = &$oSession;
  
    
    // end constructor
    }
    
    /**
     * This function is the basic zoom point function.  All other zoom related
     * functions will use this and the zoomRectngle function as the base.  
	 * If the map session's MaxExtents have been set then the zoom will be 
	 * limited accordingly.  
     *
     * @param nFactor integer - The zoom factor (negative is zoom out).
     * @param nX integer - The x pixel value.
     * @param nY integer - The y pixel value.
     */
    function zoomPoint( $nFactor, $nX, $nY )
    {
       
        $oMap = $this->oSession->getMapObj();
    
        // create the extents rectangle object
        $oCurrentExt = ms_newrectobj();

        // set the rectangle objects extents
        $oCurrentExt->setextent($oMap->extent->minx, 
                                $oMap->extent->miny,
                                $oMap->extent->maxx,
                                $oMap->extent->maxy);


        // create new point object
        $oPixelPos = ms_newpointobj();

        // set the click position
        $oPixelPos->setxy( $nX, $nY );

		// create a max zoom rectangle object if max extents are set
		$adMaxExtents = $this->oSession->getMaxExtents();
		if ( is_array( $adMaxExtents ) )
		{
          	// create the max extents rectangle object
            $oMaxExt = ms_newrectobj();
    
            // set the rectangle objects extents
            $oMaxExt->setextent($adMaxExtents["minX"], 
                                $adMaxExtents["minY"],
                                $adMaxExtents["maxX"],
                                $adMaxExtents["maxY"]);
								
										 
			// Record the current extents (if factor < -1) to check if the
			// zoomout had any effect on the extents.  If the zoomout had no
			// effect on the extents and the zoomout factor is greater than
			// 1 then we can assume the user was intending to zoom further,
			// but was limited due to max extents restriction.  Therefore we
			// zoom to maximum extents.
			if ( $nFactor < -1 )
			{
				// record extents
				$dTmpMinX = doubleval( $oMap->extent->minx );
				$dTmpMinY = doubleval( $oMap->extent->miny );
				$dTmpMaxX = doubleval( $oMap->extent->maxx );
				$dTmpMaxY = doubleval( $oMap->extent->maxy );
			}
					 
			// zoom as a point limited to max
       		$oMap->zoompoint( $nFactor, $oPixelPos, $oMap->width, 
                               $oMap->height, $oCurrentExt,$oMaxExt );
			
			// compare the new extents
			if ( $nFactor < -1 )
			{
				// check for a match
				if ( doubleval( $oMap->extent->minx ) == $dTmpMinX &&
					 doubleval( $oMap->extent->miny ) == $dTmpMinY &&
					 doubleval( $oMap->extent->maxx ) == $dTmpMaxX &&
					 doubleval( $oMap->extent->maxy ) == $dTmpMaxY )
				{
					// set the extents to the max because further zooming was
					// requested but nothing happened
					$oMap->setextent( $adMaxExtents["minX"], 
									  $adMaxExtents["minY"], 
									  $adMaxExtents["maxX"], 
									  $adMaxExtents["maxY"] );
														  
				}					 
			}
										   
			// reslease the rectangle object
			$oMaxExt->free();
			
								
		}
		else
		{
        	// zoom as a point with no limit
        	$oMap->zoompoint($nFactor, $oPixelPos, $oMap->width, 
                               					$oMap->height, $oCurrentExt);
		}
		

        //release the objects
        $oPixelPos->free();
             
        // release the extent objects
        $oCurrentExt->free();
         

        
    // end zoomPoint function
    }  

    /**
     * This function is the basic zoom rectangle function.  All other zoom 
     * related functions will use this and the zoomPoint function as the base.
     *
     * @param nMinX integer - The minimum x pixel value.
     * @param nMinY integer - The minimum y pixel value.
     * @param nMaxX integer - The maximum x pixel value.
     * @param nMaxY integer - The maximum y pixel value.
     */
    function zoomRectangle( $nMinX, $nMinY, $nMaxX, $nMaxY )
    {
        
        $oMap = $this->oSession->getMapObj();

		// limit the pixels to the size of the map
        if ( $nMinX < 0 ) $nMinX = 0;
        if ( $nMinX > $oMap->width ) $nMinX = $oMap->width;
        if ( $nMaxX < 0 ) $nMaxX = 0;
        if ( $nMaxX > $oMap->width ) $nMaxX = $oMap->width;			
        if ( $nMinY < 0 ) $nMinY = 0;
        if ( $nMinY > $oMap->height ) $nMinY = $oMap->height;
        if ( $nMaxY < 0 ) $nMaxY = 0;
        if ( $nMaxY > $oMap->height ) $nMaxY = $oMap->height;						
    
        // create the extents rectangle object
        $oCurrentExt = ms_newrectobj();

        // set the rectangle objects extents
        $oCurrentExt->setextent($oMap->extent->minx, 
                                $oMap->extent->miny,
                                $oMap->extent->maxx,
                                $oMap->extent->maxy);

       
        // create the rectangle pixel object
        $oPixelRect = ms_newrectobj();

        // set the pixel rectangle extent values
        $oPixelRect->setextent( $nMinX, $nMinY, $nMaxX, $nMaxY );
        //zoom as a rectangle
        $oMap->zoomrectangle($oPixelRect, $oMap->width, 
                                   $oMap->height, $oCurrentExt);
            
        //release the objects
        $oPixelRect->free();
             
        // release the extent object
        $oCurrentExt->free();
    
        
    // end zoomRectangle function
    }  

    /**
     * This function is a simple zoom-in-by-factor function.
     *
     * @param nFactor integer - The zoom factor (absolute value of this 
     *                          number will be used).
     * @param nX integer - The optional X pixel. 
     * @param nY integer - The optional Y pixel.
     */
    function zoomIn( $nFactor, $nX = "", $nY = "" )
    {
    
    
        // calculate the center pixel co-ordinates of the map if necessary
        if ( $nX == "" && $nY == "" )
        {
            $nX = $this->oSession->oMap->width/2;
            $nY = $this->oSession->oMap->height/2;
        }
        
        // zoom by factor
        $this->zoomPoint( abs($nFactor), $nX, $nY );
        
       
    // end zoomIn function
    }  

    /**
     * This function is a simple zoom-out-by-factor function.
     *
     * @param nFactor integer - The zoom factor (positive values will be 
     *                          converted to negative).
     * @param nX integer - The optional X pixel. 
     * @param nY integer - The optional Y pixel.
     */
    function zoomOut( $nFactor, $nX = "", $nY = "" )
    {
        // convert factor to negative
        $nFactor = 0 - abs( $nFactor );
    
           
        // calculate the center pixel co-ordinates of the map if necessary
        if ( $nX == "" && $nY == "" )
        {
            $nX = $this->oSession->oMap->width/2;
            $nY = $this->oSession->oMap->height/2;
        }
        
        // zoom by factor
        $this->zoomPoint( $nFactor, $nX, $nY );
        
            
    // end zoomOut function
    }  

    /**
     * This function zooms to the scale given at the center of the map or
	 * at the given point.
     * 
     * @param $nScale interger - The scale to zoom to.
     * @param $nX integer - The optional x pixel position to center at.
     * @param $nY integer - The optional y pixel position to center at.
     **/
    function zoomScale( $nScale, $nX = "", $nY = "" )
    {
      
        // calculate the center pixel co-ordinates of the map if necessary
        if ( $nX == "" && $nY == "" )
        {
            $nX = $this->oSession->oMap->width/2;
            $nY = $this->oSession->oMap->height/2;
        }
        
        $oPixelPos = ms_newpointobj();
        
		//set the click position
        $oPixelPos->setxy($nX,$nY);

        // create new rectangle object
        $oRect = ms_newrectobj();
        
        // get current map extents
        $dMapMinX = $this->oSession->oMap->extent->minx;
        $dMapMinY = $this->oSession->oMap->extent->miny;
        $dMapMaxX = $this->oSession->oMap->extent->maxx;
        $dMapMaxY = $this->oSession->oMap->extent->maxy;

        // set the extents of the rectangle
        $oRect->setextent($dMapMinX, $dMapMinY, $dMapMaxX, $dMapMaxY);
        
        // call the zoomscale function
        $this->oSession->oMap->zoomscale($nScale, $oPixelPos, 
		  $this->oSession->oMap->width, $this->oSession->oMap->height, $oRect);
		        
    // end zoomIn function
    }  	
	
    /**
     * This function will update the current map object's extents to cause a
     * pan movment in the direction indicated..
     *
     * @param nDirection integer - The pan direction (0-7) as defined at the 
     *                             top of this class.
     * @param nFactor integer - The optional pan factor.  It is the number 
     *                          of full map screens (either width or height) 
     *                          to pan.  The default value is 0.5. The absolute
     *                          value of the pan factor will be used.
     */
    function pan( $nDirection, $nFactor = 0.5 )
    {
       
        // get the map object
        $oMap = $this->oSession->getMapObj();
        
        // determine the center pixel co-ordinates of the map
        $nX = $oMap->width/2;
        $nY = $oMap->height/2;      
        
       
        // determine the number of pixels to move in both directions
        $nDeltaX = abs( $nFactor ) * $oMap->width;
        $nDeltaY = abs( $nFactor ) * $oMap->height;
        
        // calculate the co-ordinates of the pan
        switch ($nDirection) {
            
            // North
            case 0:
                // move the center co-ordinates
                $nY = $nY - $nDeltaY;
                break;
            
            // North East
            case 1:
                // move the center co-ordinates
                $nX = $nX + $nDeltaX;
                $nY = $nY - $nDeltaY;
                break;

            // East
            case 2:
                // move the center co-ordinates
                $nX = $nX + $nDeltaX;
                break;
                
            // South East
            case 3:
                // move the center co-ordinates
                $nX = $nX + $nDeltaX;
                $nY = $nY + $nDeltaY;
                break;
                
            // South
            case 4:
                // move the center co-ordinates
                $nY = $nY + $nDeltaY;
                break;
                
            // South West
            case 5:
                // move the center co-ordinates
                $nX = $nX - $nDeltaX;
                $nY = $nY + $nDeltaY;
                break;
                
            // West
            case 6:
                // move the center co-ordinates
                $nX = $nX - $nDeltaX;
                break;
                
            // North West
            case 7:
                // move the center co-ordinates
                $nX = $nX - $nDeltaX;
                $nY = $nY - $nDeltaY;
                break;

            // invalid direction
            default:
                // exit function
                return;
        } 
        
        // execute the pan by zooming with a factor of 1
        $this->zoomPoint( 1, $nX, $nY );
        
    // end pan function
    }  

    /**
     * This function modifies the extents to cause a recentering on the 
     * requested pixel co-ordinates.
     *
     * @param nX integer - The x pixel co-ordinate to recentre to.
     * @param nY integer - The y pixel co-ordinate to recentre to.
     */
    function recentre( $nX, $nY )
    {
        // zoom with a factor of 1 to recentre
        $this->zoomPoint( 1, $nX, $nY );
    }      

// end MapNavigator class    
}

?>