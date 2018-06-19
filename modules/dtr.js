angular.module('dtr-module',[]).service('dtr',function() {
	
	function dtr() {		
		
		var self = this;		
		
		self.print = function(scope,dtr) {
		
		var months = {
			"01": "January",
			"02": "February",
			"03": "March",
			"04": "April",
			"05": "May",
			"06": "June",
			"07": "July",
			"08": "August",
			"09": "September",
			"10": "October",
			"11": "November",
			"12": "December"
		};		
		
		(function(API){
			API.myText = function(txt, options, x, y, col2 = false) {
				options = options ||{};
				/* Use the options align property to specify desired text alignment
				 * Param x will be ignored if desired text alignment is 'center'.
				 * Usage of options can easily extend the function to apply different text 
				 * styles and sizes 
				*/
				if( options.align == "center" ){
					// Get current font size
					var fontSize = this.internal.getFontSize();

					// Get page width
					// var pageWidth = this.internal.pageSize.width;
					var pageWidth = 306;

					// Get the actual text's width
					/* You multiply the unit width of your string by your font size and divide
					 * by the internal scale factor. The division is necessary
					 * for the case where you use units other than 'pt' in the constructor
					 * of jsPDF.
					*/
					txtWidth = this.getStringUnitWidth(txt)*fontSize/this.internal.scaleFactor;

					// Calculate text's x coordinate
					x = ( pageWidth - txtWidth ) / 2;
					if (col2) x += pageWidth;
				}

				// Draw text at x,y
				this.text(txt,x,y);
			}
		})(jsPDF.API);
			
			var doc = new jsPDF({
						orientation: 'portrait',
						unit: 'pt',
						format: [792, 612]
					  });

			var columns = [
				{title: "Day", dataKey: "day"},
				{title: "Time In", dataKey: "morning_in"},
				{title: "Time Out", dataKey: "morning_out"},
				{title: "Time In", dataKey: "afternoon_in"},
				{title: "Time Out", dataKey: "afternoon_out"},
				{title: "Tardiness", dataKey: "tardiness"},
				{title: "Undertime", dataKey: "undertime"}
			];

			var rows = dtr.logs;

			// Cut lengthwise
			doc.setDrawColor(225,225,225);
			doc.line(306,0,306,792);

			doc.setFontSize(10);
			doc.setTextColor(40,40,40);
			doc.text(103, 20, 'Municipality of San Juan');
			doc.text(409, 20, 'Municipality of San Juan');

			// Line
			doc.setDrawColor(100,100,100);
			doc.line(23,40,283,40);
			doc.line(329,40,589,40);

			doc.setFontSize(13);
			doc.setTextColor(20,20,20);
			doc.text(90, 55, 'DAILY TIME RECORD');
			doc.text(396, 55, 'DAILY TIME RECORD');

			// Name
			doc.setFontSize(10);
			doc.setTextColor(40,40,40);
			var name = dtr.info.first_name + ' ' + dtr.info.middle_name + '. ' + dtr.info.last_name;
			doc.myText(name,{align: "center"},306,83);
			doc.myText(name,{align: "center"},306,83,true);

			// Line
			doc.setDrawColor(100,100,100);
			doc.line(23,87,283,87);
			doc.line(329,87,589,87);

			// (Name)
			doc.setFontSize(8);
			doc.setTextColor(100,100,100);
			doc.myText("(Name)",{align: "center"},306,95);
			doc.myText("(Name)",{align: "center"},306,95,true);

			// For the month of
			doc.setFontType('italic');
			doc.setTextColor(80,80,80);
			doc.text(25, 110, 'For the month of: ');
			doc.text(334, 110, 'For the month of: ');

			doc.setFontSize(9);
			doc.setFontType('bold');			
			doc.text(90, 110, months[dtr.month]+', '+dtr.year);		
			doc.text(399, 110, months[dtr.month]+', '+dtr.year);		

			// Office Hours
			doc.setFontSize(8);
			doc.setFontType('italic');			
			doc.text(25, 122, 'Office Hours for arrival: ');
			doc.text(334, 122, 'Office Hours for arrival: ');

			// Regular days
			doc.text(190, 122, 'Regular days: ');
			doc.text(496, 122, 'Regular days: ');

			// Saturdays
			doc.text(190, 134, 'Saturdays: ');
			doc.text(496, 134, 'Saturdays: ');

			// I CERTIFY
			doc.setFontType('normal');
			doc.text(45, 650, "I CERTIFY on my hour that the above's a true and correct report");
			doc.text(25, 663, "of the hour of work performed, record of which was made daily at the");
			doc.text(25, 676, "time of arrival at and departure from office.");

			doc.text(354, 650, "I CERTIFY on my hour that the above's a true and correct report");
			doc.text(334, 663, "of the hour of work performed, record of which was made daily at the");
			doc.text(334, 676, "time of arrival at and departure from office.");

			// Line
			doc.setDrawColor(100,100,100);
			doc.line(23,700,283,700);
			doc.line(329,700,589,700);

			// Verified
			doc.text(25, 710, "Verified as to the prescribed office hours");
			doc.text(334, 710, "Verified as to the prescribed office hours");

			// Line
			doc.setDrawColor(100,100,100);
			doc.line(50,745,260,745);
			doc.line(356,745,566,745);

			// In Charge
			doc.setFontSize(10);
			doc.myText("In Charge",{align: "center"},306,755);
			doc.myText("In Charge",{align: "center"},306,755,true);

			doc.autoTable(columns, rows, {
				// tableLineColor: [189, 195, 199],
				// tableLineWidth: 0.75,
				margin: {top: 140, left: 23},
				tableWidth: 260,
				columnStyles: {
					day: {columnWidth: 30},
					morning_in: {columnWidth: 45},
					morning_out: {columnWidth: 45},
					afternoon_in: {columnWidth: 45},
					afternoon_out: {columnWidth: 45},
					tardiness: {columnWidth: 50}
				},
				styles: {
					lineColor: [75, 75, 75],
					lineWidth: 0.50,
					cellPadding: 3
				},
				headerStyles: {
					halign: 'center',		
					fillColor: [191, 191, 191],
					textColor: 50,
					fontSize: 8
				},
				bodyStyles: {
					halign: 'center',
					fillColor: [255, 255, 255],
					textColor: 50,
					fontSize: 8
				},
				alternateRowStyles: {
					fillColor: [255, 255, 255]
				}
			});

			doc.autoTable(columns, rows, {
				// tableLineColor: [189, 195, 199],
				// tableLineWidth: 0.75,
				margin: {top: 140, left: 329},
				tableWidth: 260,
				columnStyles: {
					day: {columnWidth: 30},
					morning_in: {columnWidth: 45},
					morning_out: {columnWidth: 45},
					afternoon_in: {columnWidth: 45},
					afternoon_out: {columnWidth: 45},
					tardiness: {columnWidth: 50}
				},
				styles: {
					lineColor: [75, 75, 75],
					lineWidth: 0.50,
					cellPadding: 3
				},
				headerStyles: {
					halign: 'center',		
					fillColor: [191, 191, 191],
					textColor: 50,
					fontSize: 8
				},
				bodyStyles: {
					halign: 'center',
					fillColor: [255, 255, 255],
					textColor: 50,
					fontSize: 8
				},
				alternateRowStyles: {
					fillColor: [255, 255, 255]
				}
			});

			var blob = doc.output("blob");
			window.open(URL.createObjectURL(blob));	

		};
		
	};
	
	return new dtr();
	
});