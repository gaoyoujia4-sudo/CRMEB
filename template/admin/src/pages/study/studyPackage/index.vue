<template>
  <div>
    <el-card :bordered="false" shadow="never" class="ivu-mb-16" :body-style="{ padding: 0 }">
      <div class="padding-add">
        <el-form
          ref="formValidate"
          :model="fromData"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
          inline
        >
          <el-form-item label="套餐名称：">
            <el-input
              clearable
              placeholder="请输入套餐名称"
              v-model="fromData.keywords"
              class="form_content_width"
              @keyup.enter.native="userSearchs"
            />
          </el-form-item>
          <el-form-item label="团型：">
            <el-select v-model="fromData.group_type" placeholder="请选择" clearable class="form_content_width" @change="userSearchs">
              <el-option label="全部" :value="0" />
              <el-option label="私家团" :value="1" />
              <el-option label="拼团" :value="2" />
            </el-select>
          </el-form-item>
          <el-form-item label="场次：">
            <el-select v-model="fromData.session_type" placeholder="请选择" clearable class="form_content_width" @change="userSearchs">
              <el-option label="全部" :value="0" />
              <el-option label="上午场" :value="1" />
              <el-option label="下午场" :value="2" />
              <el-option label="一日游" :value="3" />
            </el-select>
          </el-form-item>
          <el-form-item label="状态：">
            <el-select v-model="fromData.status" placeholder="请选择" clearable class="form_content_width" @change="userSearchs">
              <el-option label="全部" value="" />
              <el-option label="上架" :value="1" />
              <el-option label="下架" :value="0" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" v-db-click @click="userSearchs">查询</el-button>
            <el-button v-db-click @click="reset">重置</el-button>
          </el-form-item>
        </el-form>
      </div>
    </el-card>
    <el-card :bordered="false" shadow="never">
      <el-button type="primary" v-db-click @click="add">添加套餐</el-button>

      <el-table
        :data="packageList"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
      >
        <el-table-column label="ID" prop="id" width="70">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="套餐名称" prop="title" min-width="180">
          <template slot-scope="scope">
            <span>{{ scope.row.title }}</span>
          </template>
        </el-table-column>
        <el-table-column label="研学商品" prop="store_name" min-width="160">
          <template slot-scope="scope">
            <span>{{ scope.row.store_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="团型" min-width="90">
          <template slot-scope="scope">
            <el-tag v-if="scope.row.group_type == 1" type="success">私家团</el-tag>
            <el-tag v-else-if="scope.row.group_type == 2" type="warning">拼团</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="场次" min-width="90">
          <template slot-scope="scope">
            <span v-if="scope.row.session_type == 1">上午场</span>
            <span v-else-if="scope.row.session_type == 2">下午场</span>
            <span v-else-if="scope.row.session_type == 3">一日游</span>
          </template>
        </el-table-column>
        <el-table-column label="套餐价" prop="price" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="划线原价" prop="ot_price" min-width="90">
          <template slot-scope="scope">
            <span>{{ scope.row.ot_price }}</span>
          </template>
        </el-table-column>
        <el-table-column label="排序" prop="sort" min-width="70">
          <template slot-scope="scope">
            <span>{{ scope.row.sort }}</span>
          </template>
        </el-table-column>
        <el-table-column label="状态" prop="status" min-width="120">
          <template slot-scope="scope">
            <el-switch
              class="defineSwitch"
              :active-value="1"
              :inactive-value="0"
              v-model="scope.row.status"
              :value="scope.row.status"
              @change="onchangeStatus(scope.row)"
              size="large"
              active-text="上架"
              inactive-text="下架"
            >
            </el-switch>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="120" fixed="right">
          <template slot-scope="scope">
            <a v-db-click @click="edit(scope.row)">编辑</a>
            <el-divider direction="vertical"></el-divider>
            <a v-db-click @click="del(scope.row)">删除</a>
          </template>
        </el-table-column>
      </el-table>
      <div class="acea-row row-right page">
        <pagination
          v-if="total"
          :total="total"
          :page.sync="fromData.page"
          :limit.sync="fromData.limit"
          @pagination="getList"
        />
      </div>
    </el-card>

    <el-dialog :visible.sync="modals" closable :title="isEdit ? '编辑套餐' : '添加套餐'" width="660" @close="cancel">
      <el-form label-position="right" size="small" ref="form" :rules="rules" :model="form" label-width="100px">
        <el-form-item label="研学商品：" prop="product_id">
          <el-input v-model="form.store_name" class="w-420" placeholder="请选择研学商品" readonly>
            <el-button slot="append" @click="chooseProduct">选择商品</el-button>
          </el-input>
        </el-form-item>
        <el-form-item label="团型：" prop="group_type">
          <el-radio-group v-model="form.group_type">
            <el-radio :label="1">私家团</el-radio>
            <el-radio :label="2">拼团</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="场次：" prop="session_type">
          <el-radio-group v-model="form.session_type">
            <el-radio :label="1">上午场</el-radio>
            <el-radio :label="2">下午场</el-radio>
            <el-radio :label="3">一日游</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="套餐名称：" prop="title">
          <el-input v-model="form.title" class="w-420" placeholder="如：不指定老师1-3人（不含票）"></el-input>
        </el-form-item>
        <el-form-item label="套餐价：" prop="price">
          <el-input-number v-model="form.price" :min="0" :precision="2" :max="999999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="划线原价：" prop="ot_price">
          <el-input-number v-model="form.ot_price" :min="0" :precision="2" :max="999999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="排序：">
          <el-input-number v-model="form.sort" :min="0" :max="999" class="w-420"></el-input-number>
        </el-form-item>
        <el-form-item label="是否上架：">
          <el-switch v-model="form.status" :active-value="1" :inactive-value="0" size="large">
            <span slot="open">上架</span>
            <span slot="close">下架</span>
          </el-switch>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancel">取 消</el-button>
        <el-button type="primary" v-db-click @click="saveConfirm">确 定</el-button>
      </span>
    </el-dialog>

    <!-- 选择商品 -->
    <el-dialog :visible.sync="goodsModals" title="选择研学商品" class="paymentFooter" width="1000px">
      <goods-list ref="goodslist" :cate-id="[41]" @getProductId="getProductId"></goods-list>
    </el-dialog>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import goodsList from '@/components/goodsList/index';
import {
  studyPackageListApi,
  studyPackageInfoApi,
  studyPackageSaveApi,
  studyPackageDelApi,
} from '@/api/study';
export default {
  name: 'studyPackage',
  components: {
    goodsList,
  },
  data() {
    return {
      loading: false,
      fromData: {
        page: 1,
        limit: 15,
        keywords: '',
        group_type: 0,
        session_type: 0,
        status: '',
      },
      packageList: [],
      total: 0,
      modals: false,
      goodsModals: false,
      isEdit: false,
      form: {
        id: 0,
        product_id: 0,
        store_name: '',
        group_type: 1,
        session_type: 1,
        title: '',
        price: 0,
        ot_price: 0,
        status: 1,
        sort: 0,
      },
      rules: {
        product_id: [{ required: true, message: '请选择研学商品', trigger: 'change' }],
        group_type: [{ required: true, message: '请选择团型', trigger: 'change' }],
        session_type: [{ required: true, message: '请选择场次', trigger: 'change' }],
        title: [{ required: true, message: '请输入套餐名称', trigger: 'blur' }],
        price: [{ required: true, message: '请输入套餐价', trigger: 'blur' }],
      },
    };
  },
  computed: {
    ...mapState('media', ['isMobile']),
    labelWidth() {
      return this.isMobile ? undefined : '90px';
    },
    labelPosition() {
      return this.isMobile ? 'top' : 'right';
    },
  },
  mounted() {
    this.getList();
  },
  methods: {
    // 添加
    add() {
      this.modals = true;
      this.isEdit = false;
    },
    // 选择商品弹窗
    chooseProduct() {
      this.goodsModals = true;
    },
    // 商品选择回调
    getProductId(row) {
      this.goodsModals = false;
      this.form.product_id = row.id;
      this.form.store_name = row.store_name;
      // 自动带出商品价格：套餐价默认取商品现价，划线原价取商品原价（可手动改）
      this.form.price = Number(row.price || 0);
      this.form.ot_price = Number(row.ot_price || row.price || 0);
    },
    // 编辑
    edit(row) {
      studyPackageInfoApi(row.id)
        .then((res) => {
          let info = res.data.info;
          this.form = {
            id: info.id,
            product_id: info.product_id,
            store_name: info.store_name || row.store_name || '',
            group_type: Number(info.group_type),
            session_type: Number(info.session_type),
            title: info.title,
            price: Number(info.price),
            ot_price: Number(info.ot_price),
            status: info.status,
            sort: Number(info.sort),
          };
          this.isEdit = true;
          this.modals = true;
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 删除
    del(row) {
      let delfromData = {
        title: '删除套餐',
        num: 0,
        url: `study/package/del/${row.id}`,
        method: 'DELETE',
        ids: '',
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 列表
    getList() {
      this.loading = true;
      studyPackageListApi(this.fromData)
        .then(async (res) => {
          let data = res.data;
          this.packageList = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$message.error(res.msg);
        });
    },
    // 搜索
    userSearchs() {
      this.fromData.page = 1;
      this.getList();
    },
    // 重置
    reset() {
      this.fromData.keywords = '';
      this.fromData.group_type = 0;
      this.fromData.session_type = 0;
      this.fromData.status = '';
      this.fromData.page = 1;
      this.getList();
    },
    // 修改状态
    onchangeStatus(row) {
      studyPackageSaveApi(row.id, {
        product_id: row.product_id,
        group_type: row.group_type,
        session_type: row.session_type,
        title: row.title,
        price: row.price,
        ot_price: row.ot_price,
        status: row.status,
        sort: row.sort,
      })
        .then(async (res) => {
          this.$message.success(res.msg);
        })
        .catch((res) => {
          this.$message.error(res.msg);
        });
    },
    // 确认保存
    saveConfirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          let data = { ...this.form };
          studyPackageSaveApi(data.id, data)
            .then((res) => {
              this.$message.success(res.msg);
              this.modals = false;
              this.cancel();
              this.fromData.page = 1;
              this.getList();
            })
            .catch((res) => {
              this.$message.error(res.msg);
            });
        }
      });
    },
    // 取消
    cancel() {
      this.form = {
        id: 0,
        product_id: 0,
        store_name: '',
        group_type: 1,
        session_type: 1,
        title: '',
        price: 0,
        ot_price: 0,
        status: 1,
        sort: 0,
      };
      this.modals = false;
    },
  },
};
</script>
