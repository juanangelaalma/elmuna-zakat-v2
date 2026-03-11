import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-react';
import { useState } from 'react';

interface DatePickerProps {
    date: any,
    setDate: (date: any) => void,
    className?: string,
    name: string
}

const DatePicker = ({ date, setDate, className, name = 'date' }: DatePickerProps) => {
    const [open, setOpen] = useState(false);

    const handleSetDate = (newDate: any) => {
        if (!newDate) return;
        setDate(format(newDate, 'yyyy-MM-dd'))
        setOpen(false)
    }

    const parsedDate = date ? new Date(date) : undefined;

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    data-empty={!date}
                    className={`h-12 w-full justify-start text-left font-normal data-[empty=true]:text-muted-foreground ${className}`}
                >
                    <CalendarIcon />
                    {parsedDate ? (
                        format(parsedDate, 'PPP')
                    ) : (
                        <span>Pilih Tanggal</span>
                    )}

                    <input type='hidden' name={name} value={date} />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-auto p-0">
                <Calendar
                    mode="single"
                    selected={parsedDate}
                    onSelect={handleSetDate}
                />
            </PopoverContent>
        </Popover>
    );
};

export default DatePicker;
