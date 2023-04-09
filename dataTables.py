from django.db.models import Q


class DataTables(object):
    def __init__(self, data):
        self.draw = int(data.get('draw', 0))
        self.start = int(data.get('start', 0))
        self.length = int(data.get('length', 0))
        self.search_key = data.get('search[value]')
        self.order_column_index = data.get('order[0][column]')
        self.order_column = data.get('order[0][dir]')

        self.listColumnsOrder = data.get('listColumnsOrder')
        self.listColumnsLooking = data.get('listColumnsLooking')
        self.listColumnsGetAll = data.get('listColumnsGetAll')

        self.filterData = data.get('filterData')
        self.model = data.get('model')

        self.q = Q()
        self.nameModel = ''
        self.lengthAll = ''
        self.dic = {}

    def process(self):
        if self.search_key:
            for lcl in self.listColumnsLooking:
                self.q |= Q(**{lcl: self.search_key})
            self.nameModel = self.model.objects.filter(self.q)
        else:
            preparedOrder = f'{self.listColumnsOrder[int(self.order_column_index)]}' if self.order_column == 'asc' else f'-{self.listColumnsOrder[int(self.order_column_index)]}'
            self.nameModel = self.model.objects.filter(**self.filterData).order_by(f'{preparedOrder}')[self.start:self.start+self.length]
        self.nameModel = list(self.nameModel.values(*self.listColumnsGetAll))
        return self.returnData()

    def returnData(self):
        self.lengthAll = len(self.model.objects.filter(**self.filterData))
        self.dic['recordsTotal'] = self.lengthAll
        self.dic['recordsFiltered'] = self.lengthAll
        self.dic['draw'] = self.draw
        self.dic['data'] = self.nameModel
        return self.dic
