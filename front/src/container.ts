import {Chart} from "./charts";
import {TraceDialog} from "./traceDialog";

class ContainerClass {
    chart: Chart;
    traceDialog: TraceDialog;
}

var ContainerInstance = new ContainerClass();
export var Container = ContainerInstance;
