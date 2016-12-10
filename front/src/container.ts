import {TraceDialog} from "./traceDialog";

class ContainerClass {
    traceDialog: TraceDialog;
}

const ContainerInstance = new ContainerClass();
export let Container = ContainerInstance;
