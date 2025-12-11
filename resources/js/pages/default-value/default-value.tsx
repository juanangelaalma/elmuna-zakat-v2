import DefaultValueController from '@/actions/App/Http/Controllers/DefaultValueController';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { defaultValue } from '@/routes';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Default Value',
        href: defaultValue().url,
    },
];

export default function DefaultValue() {
    const { defaultValue: data } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="grid gap-6 lg:grid-cols-3">
                {/* Form Card */}
                <div className="rounded-xl p-6 dark:bg-sidebar-accent">
                    <Form
                        {...DefaultValueController.update.form()}
                        options={{
                            preserveScroll: true,
                        }}
                        className="space-y-6"
                    >
                        {({ processing, recentlySuccessful, errors }) => (
                            <>
                                <div className="mb-10 space-y-3">
                                    <HeadingSmall
                                        title="Penjualan Beras"
                                        description="Update penjualan beras default value"
                                    />

                                    <div className="grid gap-2">
                                        <Label htmlFor="rice_sales_quantity">
                                            Quantity
                                        </Label>

                                        <Input
                                            id="rice_sales_quantity"
                                            className="mt-1 block w-full"
                                            name="rice_sales_quantity"
                                            required
                                            type="number"
                                            placeholder="Masukkan quantity"
                                            value={
                                                data.rice_sales_quantity
                                            }
                                        />

                                        <InputError
                                            className="mt-2"
                                            message={errors.rice_sales_quantity}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="rice_sales_amount">
                                            Amount
                                        </Label>

                                        <Input
                                            id="rice_sales_amount"
                                            className="mt-1 block w-full"
                                            name="rice_sales_amount"
                                            required
                                            type="number"
                                            placeholder="Masukkan amount"
                                            value={
                                                data.rice_sales_amount
                                            }
                                        />

                                        <InputError
                                            className="mt-2"
                                            message={errors.rice_sales_amount}
                                        />
                                    </div>
                                </div>

                                <div className="mb-10 space-y-3">
                                    <HeadingSmall
                                        title="Beras"
                                        description="Update beras default value"
                                    />

                                    <div className="grid gap-2">
                                        <Label htmlFor="rice_quantity">
                                            Quantity
                                        </Label>

                                        <Input
                                            id="rice_quantity"
                                            className="mt-1 block w-full"
                                            name="rice_quantity"
                                            required
                                            type="number"
                                            placeholder="Masukkan quantity"
                                            value={data.rice_quantity}
                                        />

                                        <InputError
                                            className="mt-2"
                                            message={errors.rice_quantity}
                                        />
                                    </div>
                                </div>

                                <div className="mb-10 space-y-3">
                                    <HeadingSmall
                                        title="Fidyah"
                                        description="Update fidyah default value"
                                    />

                                    <div className="grid gap-2">
                                        <Label htmlFor="fidyah_quantity">
                                            Quantity
                                        </Label>

                                        <Input
                                            id="fidyah_quantity"
                                            className="mt-1 block w-full"
                                            name="fidyah_quantity"
                                            required
                                            type="number"
                                            placeholder="Masukkan quantity"
                                            value={data.fidyah_quantity}
                                        />

                                        <InputError
                                            className="mt-2"
                                            message={errors.fidyah_quantity}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="fidyah_amount">
                                            Amount
                                        </Label>

                                        <Input
                                            id="fidyah_amount"
                                            className="mt-1 block w-full"
                                            name="fidyah_amount"
                                            required
                                            type="number"
                                            placeholder="Masukkan amount"
                                            value={data.fidyah_amount}
                                        />

                                        <InputError
                                            className="mt-2"
                                            message={errors.fidyah_amount}
                                        />
                                    </div>
                                </div>

                                <div className="mb-10 space-y-3">
                                    <HeadingSmall
                                        title="Unit"
                                        description="Update unit default value"
                                    />

                                    <div className="grid gap-2">
                                        <Label htmlFor="unit">Unit</Label>

                                        <Input
                                            id="unit"
                                            className="mt-1 block w-full"
                                            name="unit"
                                            required
                                            type="text"
                                            placeholder="Masukkan unit"
                                            value={data.unit}
                                        />

                                        <InputError
                                            className="mt-2"
                                            message={errors.unit}
                                        />
                                    </div>
                                </div>

                                <div className="flex items-center gap-4">
                                    <Button
                                        disabled={processing}
                                        data-test="update-profile-button"
                                    >
                                        Save
                                    </Button>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-neutral-600">
                                            Saved
                                        </p>
                                    </Transition>
                                </div>
                            </>
                        )}
                    </Form>
                </div>
            </div>
        </AppLayout>
    );
}
