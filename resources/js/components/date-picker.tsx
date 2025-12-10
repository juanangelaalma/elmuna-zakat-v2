import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-react';

interface DatePickerProps {
    date: any,
    setDate: (date: any) => void,
    className?: string,
}

const DatePicker = ({ date, setDate, className }: DatePickerProps) => {
    return (
        <Popover>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    data-empty={!date}
                    className={`h-12 w-full justify-start text-left font-normal data-[empty=true]:text-muted-foreground ${className}`}
                >
                    <CalendarIcon />
                    {date ? (
                        format(date, 'PPP')
                    ) : (
                        <span>Pilih Tanggal</span>
                    )}
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0">
                <Calendar
                    mode="single"
                    selected={date}
                    onSelect={(date) => setDate(date)}
                />
            </PopoverContent>
        </Popover>
    );
};

export default DatePicker;
