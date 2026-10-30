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
          <el-form-item label="讲师信息：">
            <el-input
              clearable
              placeholder="请输入姓名/电话"
              v-model="fromData.keywords"
              class="form_content_width"
              @keyup.enter.native="userSearchs"
            />
          </el-form-item>
          <el-form-item label="状态：">
            <el-select v-model="fromData.status" placeholder="请选择" clearable class="form_content_width" @change="userSearchs">
              <el-option label="全部" value="" />
              <el-option label="开启" :value="1" />
              <el-option label="关闭" :value="0" />
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
      <el-button type="primary" v-db-click @click="add">添加讲师</el-button>

      <el-table
        :data="lecturerList"
        ref="table"
        class="mt14"
        v-loading="loading"
        highlight-current-row
      >
        <el-table-column label="ID" prop="id" width="80">
          <template slot-scope="scope">
            <span>{{ scope.row.id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="讲师姓名" prop="name" min-width="140">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="联系电话" prop="phone" min-width="140">
          <template slot-scope="scope">
            <span>{{ scope.row.phone }}</span>
          </template>
        </el-table-column>
        <el-table-column label="讲师简介" prop="intro" min-width="220">
          <template slot-scope="scope">
            <span>{{ scope.row.intro }}</span>
          </template>
        </el-table-column>
        <el-table-column label="排序" prop="sort" min-width="80">
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
              active-text="开启"
              inactive-text="关闭"
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

    <el-dialog :visible.sync="modals" closable :title="isEdit ? '编辑讲师' : '添加讲师'" width="560" @close="cancel">
      <el-form label-position="right" size="small" ref="form" :rules="rules" :model="form" label-width="100px">
        <el-form-item label="讲师姓名：" prop="name">
          <el-input v-model="form.name" class="w-420" placeholder="请输入讲师姓名"></el-input>
        </el-form-item>
        <el-form-item label="联系电话：" prop="phone">
          <el-input v-model="form.phone" class="w-420" placeholder="请输入联系电话"></el-input>
        </el-form-item>
        <el-form-item label="讲师简介：" prop="intro">
          <el-input v-model="form.intro" class="w-420" type="textarea" :rows="3" placeholder="请输入讲师简介"></el-input>
        </el-form-item>
        <el-form-item label="排序：">
          <el-input-number v-model="form.sort" :min="0" :max="999" class="selWidth"></el-input-number>
        </el-form-item>
        <el-form-item label="是否开启：">
          <el-switch v-model="form.status" :active-value="1" :inactive-value="0" size="large">
            <span slot="open">开启</span>
            <span slot="close">关闭</span>
          </el-switch>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancel">取 消</el-button>
        <el-button type="primary" v-db-click @click="saveConfirm">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
import { mapState } from 'vuex';
import {
  lecturerListApi,
  lecturerInfoApi,
  lecturerSaveApi,
  lecturerDelApi,
} from '@/api/study';
export default {
  name: 'lecturer',
  data() {
    return {
      loading: false,
      fromData: {
        page: 1,
        limit: 15,
        keywords: '',
        status: '',
      },
      lecturerList: [],
      total: 0,
      modals: false,
      isEdit: false,
      form: {
        id: 0,
        name: '',
        phone: '',
        intro: '',
        sort: 0,
        status: 1,
      },
      rules: {
        name: [{ required: true, message: '请输入讲师姓名', trigger: 'blur' }],
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
    // 编辑
    edit(row) {
      lecturerInfoApi(row.id)
        .then((res) => {
          this.form = res.data.info;
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
        title: '删除讲师',
        num: 0,
        url: `study/lecturer/del/${row.id}`,
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
      lecturerListApi(this.fromData)
        .then(async (res) => {
          let data = res.data;
          this.lecturerList = data.list;
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
      this.fromData.status = '';
      this.fromData.page = 1;
      this.getList();
    },
    // 修改状态
    onchangeStatus(row) {
      lecturerSaveApi(row.id, { name: row.name, phone: row.phone, intro: row.intro, sort: row.sort, status: row.status })
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
          lecturerSaveApi(this.form.id, this.form)
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
        name: '',
        phone: '',
        intro: '',
        sort: 0,
        status: 1,
      };
      this.modals = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.w-420 {
  width: 420px;
}
</style>
