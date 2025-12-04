import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover"
import { format } from "date-fns";
import { Calendar } from "@/components/ui/calendar";
import { Button } from "@/components/ui/button";
import { CalendarIcon } from "lucide-react";

interface DatePickerProps {
    data: any;
    setData: (key: string, value: any) => void;
}

const DatePicker = ({ data, setData }: DatePickerProps) => {
    return (
        <Popover>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    data-empty={!data.date}
                    className="data-[empty=true]:text-muted-foreground w-full justify-start text-left font-normal h-12"
                >
                    <CalendarIcon />
                    {data.date ? format(data.date, "PPP") : <span>Pilih Tanggal</span>}
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0">
                <Calendar mode="single" selected={data.date} onSelect={(date) => setData('date', date)} />
            </PopoverContent>
        </Popover>
    );
}

export default DatePicker;
